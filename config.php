<?php
$servername = getenv('DB_HOST') ?: 'satyam-webapp-server';
$username = getenv('DB_USER') ?: 'ngipxvlxjc';
$password = getenv('DB_PASS') ?: 'LFpawoeb6$YL$QYq';
$dbname = getenv('DB_NAME') ?: 'satyam-webapp-database';

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>