<?php
require __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$dbHost = $_ENV['DB_HOST'] ?? '127.0.0.1';
$dbPort = (int)($_ENV['DB_PORT'] ?? 3306);
$dbName = $_ENV['DB_DATABASE'] ?? '';
$dbUser = $_ENV['DB_USERNAME'] ?? '';
$dbPass = $_ENV['DB_PASSWORD'] ?? '';

$db = new mysqli($dbHost, $dbUser, $dbPass, $dbName, $dbPort);

if ($db->connect_error) {
    throw new RuntimeException('Database connection failed: ' . $db->connect_error);
}

