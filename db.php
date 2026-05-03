<?php
$host = getenv('MYSQLHOST') ?: 'mysql-iso.railway.internal';
$port = getenv('MYSQLPORT') ?: '3306';
$db   = getenv('MYSQLDATABASE') ?: 'railway';
$user = getenv('MYSQLUSER') ?: 'root';
$pass = getenv('MYSQLPASSWORD') ?: 'bwKrrcgaxiWaMSulRWZbuIXDvMJsCaKv';

try {
    $pdo = new PDO(
        "mysql:host=$host;port=$port;dbname=$db;charset=utf8",
        $user,
        $pass,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
} catch (Exception $e) {
    http_response_code(500);
    die(json_encode(['error' => 'DB connection failed: ' . $e->getMessage()]));
}

$pdo->exec("CREATE TABLE IF NOT EXISTS sv_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");
?>