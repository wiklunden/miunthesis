<?php

// Comment!
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
                $name = basename($file['name']);
                $url = 'uploads/' . $name;
                $date = date('Y-m-d H:i:s');
                $fileSize = $file['size'];
                
                // Adds new entry to database
                $stmt = $this->pdo->prepare('INSERT INTO files(name, url, file_type, file_size, upload_date) VALUES(:name, :url, :file_type, :file_size, :upload_date)');
                $stmt->execute([
                    'name' => $name,
                    'url' => $url,
                    'file_type' => $fileType,
                    'file_size' => $fileSize,
                    'upload_date' => $date,
                ]);

                header('Location: ../public/scan.php');
                exit;
            } else {
                echo 'Error uploading file.';
            }
        }
    }

    public function getUploadedFiles($sortMethod, $sortType) {
        $allowedSortColumns = ['name', 'file_type', 'file_size', 'upload_date'];
        $allowedSortTypes = ['ASC', 'DESC'];
    
        if (!in_array($sortMethod, $allowedSortColumns)) {
            header('Location: ../public/scan.php');
            exit;
        }
    
        if (!in_array($sortType, $allowedSortTypes)) {
            header('Location: ../public/scan.php');
            exit;
        }
    
        $sql = "SELECT * FROM files ORDER BY $sortMethod $sortType";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    public function getFileById($id) {
        $stmt = $this->pdo->prepare('SELECT * FROM files WHERE id = :id');
        $stmt->execute([ 'id' => $id ]);
        return $stmt->fetch();
    }
    
    public function removeFile($targetId) {
        $file = $this->getFileById($targetId);
        unlink('../' . $file['url']);
    
        $stmt = $this->pdo->prepare('DELETE FROM files WHERE id = :id');
        $stmt->execute([ 'id' => $targetId ]);
    }
}