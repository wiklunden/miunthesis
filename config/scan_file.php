<?php

session_start();

require_once('../src/Scanner/PHPScanner.php');

if (!isset($_POST['scan-file'])) {
	header('Location: ../public/scan.php');
	exit;
}

$_SESSION['file-name'] = $_POST['scan-file'];

$targetFile = '../uploads/' . $_POST['scan-file'];
$scanner = new FileScanner($targetFile);
$_SESSION['stmt-results'] = $scanner->checkPreparedStatements();

header('Location: ../public/scan_results.php');
exit;