<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require 'db.php';

$path = $_SERVER['REQUEST_URI'];
$data = json_decode(file_get_contents('php://input'), true);

if ($path === '/register' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($data['username'] ?? '');
    $password = trim($data['password'] ?? '');

    if (!$username || !$password) {
        echo json_encode(['error' => 'Missing fields']);
        exit();
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
        $stmt->execute([$username, $password]);
        echo json_encode(['success' => true, 'message' => 'User registered']);
    } catch (Exception $e) {
        http_response_code(409);
        echo json_encode(['error' => 'Username already exists']);
    }
    exit();
}

if ($path === '/login' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($data['username'] ?? '');
    $password = trim($data['password'] ?? '');

    if (!$username || !$password) {
        echo json_encode(['error' => 'Missing fields']);
        exit();
    }

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? AND password = ?");
    $stmt->execute([$username, $password]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        echo json_encode(['success' => true, 'message' => 'Login successful']);
    } else {
        http_response_code(401);
        echo json_encode(['error' => 'Invalid username or password']);
    }
    exit();
}

echo json_encode(['message' => 'SecureVault API running']);
?>