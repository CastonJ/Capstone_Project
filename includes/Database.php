<?php
class Database {
    private $host = "localhost";
    private $username = "root";
    private $password = "";
    private $database = "Capstone_Project";

    public function createConnection() {
        $connection = new mysqli(
            $this->host,
            $this->username,
            $this->password,
            $this->database
        );

        if ($connection->connect_error) {
            die("Database connection failed: " . $connection->connect_error);
        }

        return $connection;
    }
}
?>