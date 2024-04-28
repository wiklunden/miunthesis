<?php

require __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

return [
    'host' => $_ENV['DBHOST'],
    'dbname' => $_ENV['DBNAME'],
    'user' => $_ENV['DBUSER'],
    'password' => $_ENV['DBPASS'],
    'charset' => 'utf8mb4',
];