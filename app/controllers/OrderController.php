<?php

namespace app\controllers;

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Client.php';
require_once __DIR__ . '/../models/Driver.php';
require_once __DIR__ . '/../models/Car.php';
require_once __DIR__ . '/../models/Order.php';

use app\models\Car;
use app\models\Driver;
use app\models\Client;
use app\models\User;
use JetBrains\PhpStorm\NoReturn;
use PDO;
use PDOException;

class OrderController
{
    private PDO $conn;

    public function __construct(PDO $conn) {
        $this->conn = $conn;
    }

    public function getOrders(): void
    {
        try {
            $stmt = $this->conn->prepare("
            SELECT o.id, o.price, o.date, o.baby, 
                   c.number AS car_number, 
                   u_driver.phone AS driver_phone, 
                   u_client.phone AS client_phone
            FROM `Order` o
            JOIN Car c ON o.car_id = c.id
            JOIN Driver d ON o.driver_id = d.id
            JOIN Users u_driver ON d.user_id = u_driver.id
            JOIN Client cl ON o.client_id = cl.id
            JOIN Users u_client ON cl.user_id = u_client.id
            ");
            $stmt->execute();
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode([
                'success' => true,
                'data' => $data,
            ]);
            exit;
        } catch (PDOException $e) {
            error_log('Select error: ' . $e->getMessage());
            $this->respondWithError('Select error.');
        }
    }

    public function getFormData(): void
    {
        try {
            $stmt = $this->conn->query("SELECT id, number FROM Car");
            $cars = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $stmt = $this->conn->query("
            SELECT u.id, u.phone 
            FROM Users u
            JOIN Client c ON u.id = c.user_id
            ");
            $clients = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $stmt = $this->conn->query("
            SELECT u.id, u.phone 
            FROM Users u
            JOIN Driver d ON u.id = d.user_id
            ");
            $drivers = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode([
                'success' => true,
                'cars' => $cars,
                'clients' => $clients,
                'drivers' => $drivers,
            ]);
            exit;
        } catch (PDOException $e) {
            error_log('Ошибка получения данных формы: ' . $e->getMessage());
            $this->respondWithError('Ошибка загрузки данных формы.');
        }
    }

    public function addOrder(): void
    {
        try {
            $data = json_decode(file_get_contents("php://input"), true);

            if (!isset($data['price'], $data['date'], $data['car_id'], $data['driver_id'], $data['client_id'])) {
                $this->respondWithError('Заполните все обязательные поля!');
            }

            $car = $this->getCarById($data['car_id']);
            if (!$car) {
                $this->respondWithError('Машина не найдена!');
            }

            $driver = $this->getDriverById($data['driver_id']);
            if (!$driver) {
                $this->respondWithError('Водитель не найден!');
            }

            $client = $this->getClientById($data['client_id']);
            if (!$client) {
                $this->respondWithError('Клиент не найден!');
            }

            $stmt = $this->conn->prepare("
            INSERT INTO `Order` (price, date, baby, car_id, driver_id, client_id)
            VALUES (:price, :date, :baby, :car_id, :driver_id, :client_id)
            ");

            $stmt->execute([
                'price' => $data['price'],
                'date' => $data['date'],
                'baby' => !empty($data['baby']) ? 1 : 0,
                'car_id' => $car->getId(),
                'driver_id' => $driver->getId(),
                'client_id' => $client->getId(),
            ]);

            echo json_encode([
                'success' => true,
                'message' => 'Заказ успешно добавлен!',
            ]);
            exit;
        } catch (PDOException $e) {
            error_log('Ошибка добавления заказа: ' . $e->getMessage());
            $this->respondWithError('Ошибка добавления заказа.');
        }
    }

    private function getCarById(int $carId): ?Car
    {
        $stmt = $this->conn->prepare("SELECT * FROM Car WHERE id = :id");
        $stmt->execute(['id' => $carId]);
        $carData = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$carData) return null;

        return new Car($carData['id'], $carData['number'], $carData['release'], (bool)$carData['baby_seat']);
    }

    private function getDriverById(int $userId): ?Driver
    {
        $stmt = $this->conn->prepare("
        SELECT u.id AS user_id, u.phone, u.password, u.role, u.is_authenticated,
               d.id AS driver_id, d.name, d.birthday, d.rate
        FROM Users u
        JOIN Driver d ON u.id = d.user_id
        WHERE d.user_id = :id
    ");
        $stmt->execute(['id' => $userId]);
        $driverData = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$driverData) return null;

        $user = new User(
            $driverData['user_id'],
            $driverData['phone'],
            $driverData['password'],
            $driverData['role'],
            (bool)$driverData['is_authenticated']
        );

        return new Driver(
            $driverData['driver_id'],
            $user,
            $driverData['name'],
            $driverData['birthday'],
            (float)$driverData['rate']
        );
    }


    private function getClientById(int $userId): ?Client
    {
        $stmt = $this->conn->prepare("
        SELECT u.id AS user_id, u.phone, u.password, u.role, u.is_authenticated,
               c.id AS client_id, c.name, c.birthday, c.rate
        FROM Users u
        JOIN Client c ON u.id = c.user_id
        WHERE c.user_id = :id
    ");
        $stmt->execute(['id' => $userId]);
        $clientData = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$clientData) return null;

        $user = new User(
            $clientData['user_id'],
            $clientData['phone'],
            $clientData['password'],
            $clientData['role'],
            (bool)$clientData['is_authenticated']
        );

        return new Client(
            $clientData['client_id'],
            $user,
            $clientData['name'],
            $clientData['birthday'],
            (float)$clientData['rate']
        );
    }


    #[NoReturn] private function respondWithError(string $message): void
    {
        echo json_encode(['success' => false, 'error' => $message]);
        exit;
    }
}
