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
				
				if ($token === ';') {
					if (preg_match('/\$/', $collectedSQL)) {
						$feedback[] = "<span class='warning'>
						LINE $startLine: Potential SQL injection detected. Variable inclusion found in string: <span class='snippet'>$collectedSQL</span>
						<span class='suggestion'>Fix: Use prepared statements to bind parameters.</span>
						</span>";
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
		}

		return $feedback;
	}
}