<?php

require_once('../src/Common/Database.php');
require_once('../src/Common/Uploader.php');

if (isset($_POST['delete-file'])) {
    $uploader = new Uploader(new Database());

    $targetId = $_POST['delete-file'];
    $uploader->removeFile($targetId);

    header('Location: ../public/scan.php');
    exit;
}