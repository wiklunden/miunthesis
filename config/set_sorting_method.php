<?php

session_start();

if (isset($_POST['sort-button'])) {
    $sortMethod = $_POST['sort-button'];

    if (isset($_SESSION['sort']) && $_SESSION['sort'] === $sortMethod) {
        $_SESSION['sortType'] = $_SESSION['sortType'] === 'ASC' ? 'DESC' : 'ASC'; 
    }

    $_SESSION['sort'] = $sortMethod;

    header('Location: ../public/scan.php');
    exit;
}