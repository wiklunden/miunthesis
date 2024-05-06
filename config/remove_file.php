<?php

require_once('../src/Common/Database.php');
require_once('../src/Common/DatabaseFunctions.php');

if (isset($_POST['delete-file'])) {
    $db = new Database();

    $targetId = $_POST['delete-file'];
    removeFile($db, $targetId);

    header('Location: ../public/scan.php');
    exit;
}