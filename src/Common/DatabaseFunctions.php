<?php

function getUploadedFiles($db, $sortMethod, $sortType) {
    $pdo = $db->getPdo();

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
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll();
}

function getFileById($db, $id) {
    $pdo = $db->getPdo();

    $stmt = $pdo->prepare('SELECT * FROM files WHERE id = :id');
    $stmt->execute([ 'id' => $id ]);
    return $stmt->fetch();
}

function removeFile($db, $targetId) {
    $pdo = $db->getPdo();

    $file = getFileById($db, $targetId);
    unlink('../' . $file['url']);

    $stmt = $pdo->prepare('DELETE FROM files WHERE id = :id');
    $stmt->execute([ 'id' => $targetId ]);
}