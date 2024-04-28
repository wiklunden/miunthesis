<?php

require_once('../src/Common/Database.php');
require_once('../src/Common/Uploader.php');

if (isset($_POST['submit'])) {
    $db = new Database();
    $uploader = new Uploader($db);
    
    $file = $_FILES['uploaded-file'];
    $uploader->uploadFile($file);
}
