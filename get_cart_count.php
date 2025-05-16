<?php
require_once 'config.php';
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'count' => 0]);
    exit;
}

$stmt = $pdo->prepare("SELECT COUNT(*) as count FROM cart WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$result = $stmt->fetch();

echo json_encode(['success' => true, 'count' => $result['count']]);
?>