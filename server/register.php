<?php

use JetBrains\PhpStorm\NoReturn;

header('Content-Type: application/json');

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../config/config.php';

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    echo json_encode([
        'success' => false,
        'error' => 'Некорректный запрос.'
    ]);
    exit;
}


// Получение данных
$name = $_POST['name'] ?? '';
$birthday = $_POST['birthday'] ?? '';
$phone = $_POST['phone'] ?? '';
$password = $_POST['password'] ?? '';
$role = $_POST['role'] ?? '';


// Валидация входных данных
if (empty($name) || empty($birthday) || empty($phone) || empty($password) || empty($role)) {
    respondWithError('Все поля должны быть заполнены.');
}

if (!in_array($role, ['client', 'driver'])) {
    respondWithError('Некорректная роль пользователя.');
}


// Создание подключения к базе данных
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
if ($conn->connect_error) {
    respondWithError('Ошибка соединения с базой данных.');
}

$conn->set_charset("utf8mb4");


// Проверка на существование пользователя с таким номером телефона
$stmt = $conn->prepare("SELECT id FROM Users WHERE phone = ?");
$stmt->bind_param("s", $phone);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    respondWithError('Пользователь с таким номером телефона уже существует.');
}


// Хеширование пароля
$passwordHash = password_hash($password, PASSWORD_BCRYPT);


// Функция для ответа с ошибкой
#[NoReturn] function respondWithError($message): void
{
    echo json_encode([
        'success' => false,
        'error' => $message
    ]);
    exit;
}


// Функция для обработки вставки данных в таблицу и создание пользователя
#[NoReturn] function insertUser($conn, $role, $name, $birthday, $phone, $passwordHash): void
{
    // Вставка данных клиента или водителя
    if ($role === 'client') {
        $stmt = $conn->prepare("INSERT INTO Client (name, birthday) VALUES (?, ?)");
        $stmt->bind_param("ss", $name, $birthday);
        if (!$stmt->execute()) {
            respondWithError('Ошибка при добавлении клиента.');
        }
    } elseif ($role === 'driver') {
        $stmt = $conn->prepare("INSERT INTO Driver (name, birthday) VALUES (?, ?)");
        $stmt->bind_param("ss", $name, $birthday);
        if (!$stmt->execute()) {
            respondWithError('Ошибка при добавлении водителя.');
        }
    }

    // Получаем ID вставленной записи
    $user_id = $stmt->insert_id;

    // Вставляем данные в таблицу Users
    $stmt = $conn->prepare("INSERT INTO Users (phone, password, role, user_id) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sssi", $phone, $passwordHash, $role, $user_id);
    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'Регистрация успешна!',
            'redirect' => ($role === 'client') ? '/client_dashboard.html' : '/driver_dashboard.html'
        ]);
        exit;
    } else {
        respondWithError('Ошибка при добавлении пользователя в таблицу Users.');
    }
}

insertUser($conn, $role, $name, $birthday, $phone, $passwordHash);

$stmt->close();
$conn->close();
