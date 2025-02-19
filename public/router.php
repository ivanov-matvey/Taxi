<?php

use app\Auth;
use app\controllers\OrderController;
use app\controllers\UserController;

require_once __DIR__ . '/../app/controllers/UserController.php';
require_once __DIR__ . '/../app/controllers/OrderController.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../app/auth.php';

session_start();

$db = new Database();
$conn = $db->getConnection();

$method = $_SERVER['REQUEST_METHOD'];
$requestUri = explode('/', trim($_SERVER['REQUEST_URI'], '/'));

$controller = $requestUri[1] ?? '';
$action = $requestUri[2] ?? '';

$userController = new UserController($conn);
$orderController = new OrderController($conn);
$auth = new Auth();

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
    case 'auth':
        if ($action == 'check-auth') {
            $auth->checkAuth();
        }
        break;
    case 'order':
        if ($action == 'list') {
            $orderController->getOrders();
        } else if ($action == 'form-data') {
            $orderController->getFormData();
        } else if ($action == 'add') {
            $orderController->addOrder();
        }
        break;
    default:
        echo json_encode(['error' => 'Invalid controller']);
        break;
}
