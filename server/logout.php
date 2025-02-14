<?php
session_start();
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/User.php';

use Models\User;

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Пользователь не авторизован.']);
    exit;
}

try {
    $pdo = new PDO("mysql:host=" . DB_SERVER . ";dbname=" . DB_NAME . ";charset=utf8", DB_USERNAME, DB_PASSWORD);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $userId = $_SESSION['user_id'];

    $stmt = $pdo->prepare("UPDATE Users SET is_authenticated = 0 WHERE id = :id");
    $stmt->bindParam(':id', $userId);
    $stmt->execute();

    session_unset();
    session_destroy();

    echo json_encode(['success' => true, 'message' => 'Выход успешен']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => 'Ошибка при выходе.']);
}
