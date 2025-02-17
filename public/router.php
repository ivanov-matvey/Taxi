<?php

use app\controllers\UserController;

require_once __DIR__ . '/../app/controllers/UserController.php';

$db = new Database();
$conn = $db->getConnection();

$method = $_SERVER['REQUEST_METHOD'];
$requestUri = explode('/', trim($_SERVER['REQUEST_URI'], '/'));

$controller = $requestUri[1] ?? '';
$action = $requestUri[2] ?? '';

$userController = new UserController($conn);

switch ($controller) {
    case 'user':
        switch ($method) {
            case 'POST':
                if ($action === 'register') {
                    $userController->register();
                } elseif ($action === 'login') {
                    $userController->login();
                } elseif ($action === 'logout') {
                    $userController->logout();
                } else {
                    echo json_encode(['error' => 'Invalid action']);
                }
                break;
            case 'GET':
                if (isset($_GET['id'])) {
                    $userController->getUser($_GET['id']);
                } else {
                    $userController->getUsers();
                }
                break;
            default:
                echo json_encode(['error' => 'Method not allowed']);
                break;
        }
        break;
    default:
        echo json_encode(['error' => 'Invalid controller']);
        break;
}
