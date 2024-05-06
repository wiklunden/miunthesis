<?php

class FileScanner {
	private $fileContents;
	private $scopeStack = [];
	private $feedback = [];

	public function __construct($filePath) {
		if (!file_exists($filePath)) {
			throw new Exception("Failed to load file '$filePath'.");
		}
		$this->fileContents = file_get_contents($filePath);
	}

	public function checkPreparedStatements() {
		$tokens = token_get_all($this->fileContents);
		$currentScope = null; // Holds state of current function scope
	
		foreach ($tokens as $token) {
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
						$this->feedback[] = "<span class='stmt-warning'>LINE {$currentScope['prepare_line']}: You are missing an 'execute()' function for the 'prepare()' function.</span>";
					} else {
						$this->feedback[] = "<span>LINE {$currentScope['prepare_line']}: Prepared statements have been implemented correctly.</span>";
					}
				}
			}
		}

		return $this->feedback;
	}
}