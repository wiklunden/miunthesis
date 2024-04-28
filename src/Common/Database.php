<?php

class Database {
    private $pdo;

    public function __construct() {
        $config = include(__DIR__ . '/../../config/db_credentials.php');
        $dsn = "mysql:host={$config['host']};dbname={$config['dbname']};charset={$config['charset']};";

        try {
            $this->pdo = new PDO($dsn, $config['user'], $config['password']);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die('Failed connecting to the database: ' . $e->getMessage());
        }
    }

    public function getPdo() {
        return $this->pdo;
    }
}