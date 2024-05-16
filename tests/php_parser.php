<?php

require '../vendor/autoload.php';

use PhpParser\Error;
use PhpParser\ParserFactory;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;
use PhpParser\Node;
use PhpParser\PrettyPrinter;

$code = <<<'CODE'
if (move_uploaded_file($file['tmp_name'], $targetFile)) {
    // Adds new entry to database
    $stmt = $this->pdo->prepare('INSERT 1 INTO files(name, url, file_type, file_size, upload_date) VALUES(:name, :url, :file_type, :file_size, :upload_date)');
    $stmt->execute([]);

    header('Location: ../public/scan.php');
    exit;
} else {
    echo 'Error uploading file.';
}

$missedStmt = $pdo->prepare("INSERT INTO table (column) VALUES(?)");
// Missed execution
CODE;

// Parser setup
$parser = (new ParserFactory)->createForHostVersion();

try {
	$ast = $parser->parse($code);
} catch (Error $error) {
	echo "Parse error: ", $error->getMessage();
	return;
}

class SQLExecuteVisitor extends NodeVisitorAbstract {
	private $lastPrepared = null;
	private $prettyPrinter;

	public function __construct() {
		$this->prettyPrinter = new PrettyPrinter\Standard();
	}

	public function enterNode(Node $node) {
		if ($node instanceof Node\Expr\MethodCall) {
			$methodName = strtolower($node->name->toString());

			if ($methodName === 'prepare') {
				if (isset($node->args[0])) {
					$sqlQuery = $this->prettyPrinter->prettyPrintExpr($node->args[0]->value);
					$sqlQuery = strtolower($sqlQuery);
					if (strpos($sqlQuery, 'insert') !== false) {
						$this->lastPrepared = $node->var;
					}
				}
			} elseif ($methodName === 'execute') {
				if (isset($this->lastPrepared) && $node->var === $this->lastPrepared) {
					$this->lastPrepared = null;
				}
			}
		}
	}

	public function leaveNode(Node $node) {
		if ($node instanceof Node\Stmt\Expression && $this->lastPrepared !== null) {
			echo "Found an 'insert' not followed by 'execute()':\n";
			echo $node->expr->getLine() . ": " . $this->prettyPrinter->prettyPrint([$node->expr]) . "\n";
			$this->lastPrepared = null;
		}
	}
}

$traverser = new NodeTraverser();
$traverser->addVisitor(new SQLExecuteVisitor());
$traverser->traverse($ast);