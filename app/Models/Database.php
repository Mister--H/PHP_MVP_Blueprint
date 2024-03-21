<?php

namespace App\Models;

use Dotenv\Dotenv;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use PDO;
use PDOException;

class Database {
    private $dbh; // Database handler
    private $stmt;
    private $logger; // Monolog logger

    public function __construct() {
        // Initialize Dotenv and load environment variables
        $dotenv = Dotenv::createImmutable(__DIR__ . '/../..'); // Adjust the path to your .env file
        $dotenv->load();

        // Initialize Monolog logger
        $this->logger = new Logger('database');
        $this->logger->pushHandler(new StreamHandler(__DIR__ . '/../../logs/database.log', Logger::WARNING));

        // Set DSN (Data Source Name)
        $dsn = 'mysql:host=' . $_ENV['DB_HOST'] . ';dbname=' . $_ENV['DB_NAME'] . ';charset=utf8mb4';

        // Set options
        $options = [
            PDO::ATTR_PERSISTENT => true,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ];

        // Create a new PDO instance
        try {
            $this->dbh = new PDO($dsn, $_ENV['DB_USER'], $_ENV['DB_PASS'], $options);
        } catch(PDOException $e) {
            $this->logger->error("Database connection error: " . $e->getMessage());
            die("Database connection error: " . $e->getMessage());
        }
    }
    
    // Method to prepare statements
    public function query($query) {
        $this->stmt = $this->dbh->prepare($query);
    }

    // Method to bind values
    public function bind($param, $value, $type = null) {
        if (is_null($type)) {
            switch (true) {
                case is_int($value):
                    $type = PDO::PARAM_INT;
                    break;
                case is_bool($value):
                    $type = PDO::PARAM_BOOL;
                    break;
                case is_null($value):
                    $type = PDO::PARAM_NULL;
                    break;
                default:
                    $type = PDO::PARAM_STR;
            }
        }
        $this->stmt->bindValue($param, $value, $type);
    }

    // Method to execute the prepared statement
    public function execute() {
        return $this->stmt->execute();
    }

    // Method to get result set
    public function resultSet() {
        $this->execute();
        return $this->stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Method to get single record
    public function single() {
        $this->execute();
        return $this->stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Method to get row count
    public function rowCount() {
        return $this->stmt->rowCount();
    }

    // Method to get the ID of the last inserted row
    public function lastInsertId() {
        return $this->dbh->lastInsertId();
    }

    // Transaction Methods
    public function beginTransaction() {
        return $this->dbh->beginTransaction();
    }

    public function endTransaction() {
        return $this->dbh->commit();
    }

    public function cancelTransaction() {
        return $this->dbh->rollBack();
    }
}
?>