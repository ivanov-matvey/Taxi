<?php
namespace Controllers;
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/User.php';

use JetBrains\PhpStorm\NoReturn;
use Models\User;
use PDO;
use PDOException;

class LoginController
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function loginUser(array $data): void
    {
        session_start();

        $phone = $data['phone'] ?? '';
        $password = $data['password'] ?? '';

        if (empty($phone) || empty($password)) {
            $this->respondWithError('Все поля должны быть заполнены.');
        }

        try {
            $stmt = $this->db->prepare("SELECT id, password, role, is_authenticated FROM Users WHERE phone = :phone");
            $stmt->bindParam(':phone', $phone);
            $stmt->execute();

            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$user) {
                $this->respondWithError('Пользователь с таким номером не найден.');
            }

            if (!password_verify($password, $user['password'])) {
                $this->respondWithError('Неверный пароль.');
            }

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['is_authenticated'] = true;

            $updateStmt = $this->db->prepare("UPDATE Users SET is_authenticated = TRUE WHERE id = :id");
            $updateStmt->bindParam(':id', $user['id']);
            $updateStmt->execute();

            echo json_encode([
                'success' => true,
                'message' => 'Авторизация успешна!',
                'role' => $user['role'],
                'redirect' => $user['role'] === 'client' ? '/client_dashboard.html' : '/driver_dashboard.html'
            ]);
        } catch (PDOException $e) {
            error_log('Ошибка при авторизации: ' . $e->getMessage());
            $this->respondWithError('Ошибка при авторизации.');
        }
    }


    #[NoReturn] private function respondWithError(string $message): void
    {
        echo json_encode(['success' => false, 'error' => $message]);
        exit;
    }
}
