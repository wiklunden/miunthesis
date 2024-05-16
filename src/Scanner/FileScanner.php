<?php

class FileScanner {
	private $fileContents;
	private $tokens;
	private $scopeStack = [];

	public function __construct($filePath) {
		if (!file_exists($filePath)) {
			throw new Exception("Failed to load file '$filePath'.");
		}
		$this->fileContents = file_get_contents($filePath);
		$this->tokens = token_get_all($this->fileContents);
	}

	public function checkPreparedStatements() {
		$feedback = [];
		$currentScope = null; // Holds state of current function scope
	
		foreach ($this->tokens as $token) {
			if (is_array($token)) {
				$type = $token[0];
				$value = strtolower($token[1]);
				$line = $token[2];
				
				switch ($type) {
					case T_FUNCTION:
						// Initiates a new scope when encountering a function definition
						$currentScope = ['prepare' => false, 'execute' => false, 'prepare_line' => 0];
						array_push($this->scopeStack, $currentScope);
						break;
					case T_STRING:
						if ($value === 'prepare' && $currentScope && !$currentScope['prepare']) {
							// Marks the word 'prepare' as found in the current function scope
							$currentScope['prepare'] = true;
							$currentScope['prepare_line'] = $line;
							$this->scopeStack[count($this->scopeStack) - 1] = $currentScope;
						} elseif ($value === 'execute' && $currentScope && $currentScope['prepare']) {
							// Marks the word 'execute' as found after 'prepare' within the same scope
							$currentScope['execute'] = true;
							$this->scopeStack[count($this->scopeStack) - 1] = $currentScope;
						}
						break;
				}
			} elseif ($token === '}') {
				// Checks conditions at the end of a scope (function)
				$currentScope = array_pop($this->scopeStack);
				if ($currentScope && $currentScope['prepare']) {
					if (!$currentScope['execute']) {
						$feedback[] = "<span class='warning'>LINE {$currentScope['prepare_line']}: An 'execute()' function is missing for the corresponding 'prepare()' function.</span>";
					}
				}
			}
		}

		if (empty($feedback)) {
			$feedback[] = "<span>No issues found.</span>";
		}

		return $feedback;
	}

	public function checkSQLInjections() {
		$feedback = [];
		$collecting = false;
		$collectedSQL = '';
		$startLine = 0;

		foreach ($this->tokens as $token) {
			if ($collecting) {
				if (is_array($token)) {
					$collectedSQL .= $token[1];
				} else {
					$collectedSQL .= $token;
				}
				
				// A found dollarsign ($) indicates direct input of a variable
				if ($token === ';') {
					if (preg_match('/\$/', $collectedSQL)) {
						$feedback[] = "<span class='warning'>LINE $startLine: Potential SQL injection detected. Variable inclusion found in string: <span class='snippet'>$collectedSQL</span></span>";
					}
					$collecting = false;
					$collectedSQL = '';
				}
			} else {
				if (is_array($token)) {
					list($type, $value, $line) = $token;

					if ($type == T_STRING && strtolower($value) == 'prepare') {
						$collecting = true;
						$startLine = $line;
						$collectedSQL = $value;
					}
				}
			}
		}

		if (empty($feedback)) {
			$feedback[] = "<span>No risk of SQL injection detected.</span>";
		} else {
			$feedback[] = "<span class='suggestion'>
			Tips:<br>
			Use prepared statements to bind parameters.<br>
			Example of replacing variable concatenation:<br>
			Change <span class='snippet'>VALUES('. \$variable . ')'</span> to <span class='snippet'>VALUES(:variable)</span>.<br>
			Execute the statement with <span class='snippet'>execute([ 'variable' => \$variable ])</span>.<br><br>
			Read more about <a target='_blank' href='https://www.php.net/manual/en/mysqli.quickstart.prepared-statements.php'>prepared statements for PHP</a>.
			</span>";
		}

		return $feedback;
	}

	private function calculateComplexity() {
		$currentFunction = '';
		$gettingFunctionName = false;
		$functionLine = 0;
		$complexity = 0;
		$functionComplexities = [];

		foreach ($this->tokens as $token) {
			if (is_array($token)) {
				if ($token[0] == T_FUNCTION) {
					$gettingFunctionName = true;
					$functionLine = $token[2];
				} elseif ($gettingFunctionName && $token[0] == T_STRING) {
					// Captures function name
					$currentFunction = $token[1];
					$gettingFunctionName = false;
					$complexity = 0;
					$functionComplexities[$currentFunction] = ['complexity' => $complexity, 'line' => $functionLine];
				}
			} elseif ($token == '{') {
				if ($currentFunction) {
					$complexity++;
				}
			} elseif ($token == '}') {
				if ($currentFunction) {
					$functionComplexities[$currentFunction]['complexity'] = $complexity;
					$currentFunction = '';
				}
			}
		}

		return $functionComplexities;
	}

	public function checkComplexity() {
		$complexities = $this->calculateComplexity();
		$existsHighComplexities = false;
		$feedback = [];

		foreach ($complexities as $functionName => $details) {
			$complexity = $details['complexity'];
			$line = $details['line'];

			if ($complexity > 10) {
				$existsHighComplexities = true;
				$feedback[] = "<span class='complexity-red'>LINE $line: Function $functionName() has a very high complexity ($complexity). A thorough review and redesign of code structure is highly recommended.</span>";
			} elseif ($complexity > 20) {
				$feedback[] = "<span class='complexity-orange'>LINE $line: Function $functionName() has a high complexity ($complexity). Refactoring recommended.</span>" .
				"<span class='suggestion'>Tips: Reduce amount of nested loops.</span>";
			} elseif ($complexity > 50) {
				$feedback[] = "<span class='complexity-yellow'>LINE $line: Function $functionName() has moderate complexity ($complexity). Refactoring could be beneficial.</span>" .
				"<span class='suggestion'>Tips: Reduce amount of nested loops.</span>";
			} else {
				$feedback[] = "<span class='complexity-green'>LINE $line: Function $functionName() has low complexity ($complexity) and does not need refactoring.</span>";
			}
		}

		if ($existsHighComplexities) {
			$feedback[] = "<span class='suggestion'>
			Tips:<br>
			Break the function into smaller, single-purpose functions.<br>
			Remove code paths that do not affect the outcome.<br>
			Move complex expressions into their own functions.<br><br>
			Read more about <a target='_blank' href='https://linearb.io/blog/reduce-cyclomatic-complexity'>reducing cyclomatic complexity</a>.
			</span>";
		}

		return $feedback;
	}
}