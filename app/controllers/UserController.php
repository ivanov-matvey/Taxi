<?php

namespace app\controllers;

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Client.php';
require_once __DIR__ . '/../models/Driver.php';

use JetBrains\PhpStorm\NoReturn;
use PDO;
use app\models\Client;
use app\models\Driver;
use app\models\User;
use PDOException;

class UserController
{
    private PDO $conn;

    public function __construct(PDO $conn)
    {
        $this->conn = $conn;
    }

    public function register(): void
    {
        $data = json_decode(file_get_contents('php://input'), true);

        $name = $data['name'] ?? '';
        $birthday = $data['birthday'] ?? '';
        $phone = $data['phone'] ?? '';
        $password = $data['password'] ?? '';
        $role = $data['role'] ?? '';

        if (empty($name) || empty($birthday) || empty($phone) || empty($password) || empty($role)) {
            $this->respondWithError('All fields must be filled in.');
        }

        if (!in_array($role, ['client', 'driver'])) {
            $this->respondWithError('Invalid role.');
        }

        try {
            $stmt = $this->conn->prepare("SELECT id FROM Users WHERE phone = :phone");
            $stmt->bindParam(':phone', $phone);
            $stmt->execute();

            if ($stmt->fetch()) {
                $this->respondWithError('A user with this number already exists.');
            }

            $user = new User(null, $phone, $password, $role, 0);
            $phoneValue = $user->getPhone();
            $passwordValue = $user->getPassword();
            $roleValue = $user->getRole();

            $stmt = $this->conn->prepare("INSERT INTO Users (phone, password, role) VALUES (:phone, :password, :role)");
            $stmt->bindParam(':phone', $phoneValue);
            $stmt->bindParam(':password', $passwordValue);
            $stmt->bindParam(':role', $roleValue);
            $stmt->execute();

            $userId = $this->conn->lastInsertId();
            $user->setId((int)$userId);

            if ($role === 'client') {
                $client = new Client($user, $name, $birthday);
                $clientName = $client->getName();
                $clientBirthday = $client->getBirthday();
                $clientRate = $client->getRate();

                $stmt = $this->conn->prepare("INSERT INTO Client (user_id, name, birthday, rate) VALUES (:user_id, :name, :birthday, :rate)");
                $stmt->bindParam(':user_id', $userId);
                $stmt->bindParam(':name', $clientName);
                $stmt->bindParam(':birthday', $clientBirthday);
                $stmt->bindParam(':rate', $clientRate);
                $stmt->execute();
            } elseif ($role === 'driver') {
                $driver = new Driver($user, $name, $birthday);
                $driverName = $driver->getName();
                $driverBirthday = $driver->getBirthday();
                $driverRate = $driver->getRate();

                $stmt = $this->conn->prepare("INSERT INTO Driver (user_id, name, birthday, rate) VALUES (:user_id, :name, :birthday, :rate)");
                $stmt->bindParam(':user_id', $userId);
                $stmt->bindParam(':name', $driverName);
                $stmt->bindParam(':birthday', $driverBirthday);
                $stmt->bindParam(':rate', $driverRate);
                $stmt->execute();
            }

            echo json_encode([
                'success' => true,
                'message' => 'Registration successful.',
                'redirect' => '/login.html'
            ]);
        } catch (PDOException $e) {
            error_log("Registration error: " . $e->getMessage());
            $this->respondWithError('Registration error.');
        }
    }

    public function login(): void
    {
        $data = json_decode(file_get_contents('php://input'), true);

        session_start();

        $phone = $data['phone'] ?? '';
        $password = $data['password'] ?? '';

        if (empty($phone) || empty($password)) {
            $this->respondWithError('All fields must be filled in.');
        }

        try {
            $stmt = $this->conn->prepare("SELECT id, password, role, is_authenticated FROM Users WHERE phone = :phone");
            $stmt->bindParam(':phone', $phone);
            $stmt->execute();

            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$user) {
                $this->respondWithError('A user with this number was not found.');
            }

            if (!password_verify($password, $user['password'])) {
                $this->respondWithError('Invalid password.');
            }

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['is_authenticated'] = true;

            $updateStmt = $this->conn->prepare("UPDATE Users SET is_authenticated = TRUE WHERE id = :id");
            $updateStmt->bindParam(':id', $user['id']);
            $updateStmt->execute();

            echo json_encode([
                'success' => true,
                'message' => 'Authorization successful.',
                'role' => $user['role'],
                'redirect' => $user['role'] === 'client' ? '/client/dashboard.html' : '/driver/dashboard.html'
            ]);
        } catch (PDOException $e) {
            error_log('Authorization error: ' . $e->getMessage());
            $this->respondWithError('Authorization error.');
        }
    }

    public function logout(): void
    {
        session_start();

        if (!isset($_SESSION['user_id'])) {
            $this->respondWithError('The user is not authorized.');
        }

        try {
            $userId = $_SESSION['user_id'];

            $updateStmt = $this->conn->prepare("UPDATE Users SET is_authenticated = FALSE WHERE id = :id");
            $updateStmt->bindParam(':id', $userId);
            $updateStmt->execute();

            session_unset();
            session_destroy();
            session_write_close();

            echo json_encode([
                'success' => true,
                'message' => 'Logout successful',
                'redirect' => '/index.html'
            ]);
        } catch (PDOException $e) {
            error_log('Logout error: ' . $e->getMessage());
            $this->respondWithError('Logout error.');
        }
    }


    #[NoReturn] private function respondWithError(string $message): void
    {
        echo json_encode(['success' => false, 'error' => $message]);
        exit;
    }
}
