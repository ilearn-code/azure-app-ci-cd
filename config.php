<?php
$servername = getenv('DB_HOST') ?: 'localhost';
$username = getenv('DB_USER') ?: 'root';
$password = getenv('DB_PASS') ?: '';
$dbname = getenv('DB_NAME') ?: 'test';
$port = getenv('DB_PORT') ?: '3306';
$ssl_ca     = __DIR__ . '/DigiCertGlobalRootCA.crt.pem'; // Ensure file exists



try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname;port=$port;charset=utf8mb4", $username, $password, [
        PDO::MYSQL_ATTR_SSL_CA => $ssl_ca
    ]);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>