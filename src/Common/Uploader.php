<?php

class Uploader {
    private $pdo;
    private $uploadDir;

    public function __construct($database) {
        $this->pdo = $database->getPdo();
        $this->uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/thesis/uploads/';
    }
    
    public function uploadFile($file) {
        $targetFile = $this->uploadDir . basename($file['name']);
        $uploadOk = 1;
        $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

        if (file_exists($targetFile)) {
            echo 'This file already exists.';
            $uploadOk = 0;
        }

        if ($file['size'] > 5000000) {
            echo 'File exceeds size limit.';
            $uploadOk = 0;
        }

        $allowedTypes = ['txt', 'js', 'php'];
        if (!in_array($fileType, $allowedTypes)) {
            echo 'Invalid file type. Allowed file types are: TXT, JS & PHP.';
            $uploadOk = 0;
        }

        if ($uploadOk == 0) {
            echo 'File was not uploaded.';
        } else {
            if (move_uploaded_file($file['tmp_name'], $targetFile)) {
                echo 'Successfully uploaded ' . htmlspecialchars(basename($file['name'])) . '.';

                $date = date('Y-m-d H:i:s');
                // Adds new entry to database
                $stmt = $this->pdo->prepare('INSERT INTO files(url, file_type, upload_date) VALUES(:url, :file_type, :upload_date)');
                $stmt->execute([
                    'url' => $targetFile,
                    'file_type' => $fileType,
                    'upload_date' => $date,
                ]);
            } else {
                echo 'Error uploading file.';
            }
        }
    }
}