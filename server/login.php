<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../controllers/LoginController.php';

use Controllers\LoginController;

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Некорректный запрос.']);
    exit;
}

try {
    $pdo = new PDO("mysql:host=" . DB_SERVER . ";dbname=" . DB_NAME . ";charset=utf8", DB_USERNAME, DB_PASSWORD);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $controller = new LoginController($pdo);
    $controller->loginUser($_POST);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => 'Ошибка соединения с БД.']);
    exit;
}
