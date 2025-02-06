<?php
namespace Controllers;

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Client.php';
require_once __DIR__ . '/../models/Driver.php';

use JetBrains\PhpStorm\NoReturn;
use Models\User;
use Models\Client;
use Models\Driver;
use PDO;
use PDOException;

class RegisterController
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function registerUser(array $data): void
    {
        $name = $data['name'] ?? '';
        $birthday = $data['birthday'] ?? '';
        $phone = $data['phone'] ?? '';
        $password = $data['password'] ?? '';
        $role = $data['role'] ?? '';

        if (empty($name) || empty($birthday) || empty($phone) || empty($password) || empty($role)) {
            $this->respondWithError('Все поля должны быть заполнены.');
        }

        if (!in_array($role, ['client', 'driver'])) {
            $this->respondWithError('Некорректная роль.');
        }

        try {
            $stmt = $this->db->prepare("SELECT id FROM Users WHERE phone = :phone");
            $stmt->bindParam(':phone', $phone);
            $stmt->execute();

            if ($stmt->fetch()) {
                $this->respondWithError('Пользователь с таким номером уже существует.');
            }

            $user = new User(null, $phone, $password, $role);
            $phoneValue = $user->getPhone();
            $passwordValue = $user->getPassword();
            $roleValue = $user->getRole();

            $stmt = $this->db->prepare("INSERT INTO Users (phone, password, role) VALUES (:phone, :password, :role)");
            $stmt->bindParam(':phone', $phoneValue);
            $stmt->bindParam(':password', $passwordValue);
            $stmt->bindParam(':role', $roleValue);
            $stmt->execute();

            $userId = $this->db->lastInsertId();
            $user->setId((int)$userId);

            if ($role === 'client') {
                $client = new Client($user, $name, $birthday);
                $clientName = $client->getName();
                $clientBirthday = $client->getBirthday();
                $clientRate = $client->getRate();

                $stmt = $this->db->prepare("INSERT INTO Client (user_id, name, birthday, rate) VALUES (:user_id, :name, :birthday, :rate)");
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

                $stmt = $this->db->prepare("INSERT INTO Driver (user_id, name, birthday, rate) VALUES (:user_id, :name, :birthday, :rate)");
                $stmt->bindParam(':user_id', $userId);
                $stmt->bindParam(':name', $driverName);
                $stmt->bindParam(':birthday', $driverBirthday);
                $stmt->bindParam(':rate', $driverRate);
                $stmt->execute();
            }

            echo json_encode([
                'success' => true,
                'message' => 'Регистрация успешна!',
                'redirect' => ($role === 'client') ? '/client_dashboard.html' : '/driver_dashboard.html'
            ]);
        } catch (PDOException $e) {
            error_log("Ошибка при регистрации: " . $e->getMessage());
            $this->respondWithError('Ошибка при регистрации.');
        }
    }

    #[NoReturn] private function respondWithError(string $message): void
    {
        echo json_encode(['success' => false, 'error' => $message]);
        exit;
    }
}
