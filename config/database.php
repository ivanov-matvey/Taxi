<?php

class Database {
    private string $host = 'localhost';
    private string $dbName = 'taxi';
    private string $user = 'root';
    private string $password = '';
    public $conn;

    public function getConnection()
    {
        $this->conn = null;
        try {
            $conn_string = 'mysql:host=' . $this->host . ';dbname=' . $this->dbName . ";charset=utf8mb4";
            $this->conn = new PDO($conn_string, $this->user, $this->password);
        } catch (PDOException $error) {
            echo 'Connection error: ' . $error->getMessage();
        }

        return $this->conn;
    }
}