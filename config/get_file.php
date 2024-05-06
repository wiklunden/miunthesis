<?php

if (isset($_GET['filename'])) {
    $fileName = $_GET['filename'];
    $filePath = '../uploads/' . $fileName;

    if (file_exists($filePath) && is_readable($filePath)) {
        header('Content-Type: text/plain');
        readfile($filePath);
    } else {
        http_response_code(404);
        echo 'File not found or not readable.';
    }
} else {
    http_response_code(400);
    echo 'No filename specified.';
}