<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
header('Access-Control-Max-Age: 86400');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require 'db.php';

$uri = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];
$data = json_decode(file_get_contents('php://input'), true);

// POST /register
if (strpos($uri, '/register') !== false && $method === 'POST') {
    $username = trim($data['username'] ?? '');
    $password = trim($data['password'] ?? '');
    $face_descriptor = $data['face_descriptor'] ?? null;
    $credential_id = $data['credential_id'] ?? null;

    if (!$username || !$password) {
        echo json_encode(['error' => 'Missing fields']);
        exit();
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO sv_users (username, password, face_descriptor, credential_id) VALUES (?, ?, ?, ?)");
        $stmt->execute([
            $username,
            $password,
            $face_descriptor ? json_encode($face_descriptor) : null,
            $credential_id
        ]);
        echo json_encode(['success' => true, 'message' => 'User registered']);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
    }
    exit();
}

// POST /login
if (strpos($uri, '/login') !== false && $method === 'POST') {
    $username = trim($data['username'] ?? '');
    $password = trim($data['password'] ?? '');

    if (!$username || !$password) {
        echo json_encode(['error' => 'Missing fields']);
        exit();
    }

    $stmt = $pdo->prepare("SELECT * FROM sv_users WHERE username = ? AND password = ?");
    $stmt->execute([$username, $password]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        echo json_encode([
            'success' => true,
            'message' => 'Login successful',
            'face_descriptor' => $user['face_descriptor'] ? json_decode($user['face_descriptor']) : null,
            'credential_id' => $user['credential_id']
        ]);
    } else {
        http_response_code(401);
        echo json_encode(['error' => 'Invalid username or password']);
    }
    exit();
}

echo json_encode(['message' => 'SecureVault API running']);
?>