<?php

session_start();

require_once('../src/Scanner/FileScanner.php');

if (!isset($_POST['scan-file'])) {
	header('Location: ../public/scan.php');
	exit;
}

$_SESSION['file-name'] = $_POST['scan-file'];
$targetFile = '../uploads/' . $_POST['scan-file'];

try {
    $scanner = new FileScanner($targetFile);
    $_SESSION['stmt-results'] = $scanner->checkPreparedStatements();
    $_SESSION['sqli-results'] = $scanner->checkSQLInjections();
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}

header('Location: ../public/scan_results.php');
exit;