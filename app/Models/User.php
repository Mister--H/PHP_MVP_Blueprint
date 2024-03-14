<?php
namespace App\Models;

use PDO;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class User {
    private $db;
    private $logger;

    public function __construct(Database $db) {
        $this->db = $db;

        // Initialize Monolog logger for UserModel
        $this->logger = new Logger('UserModel');
        $this->logger->pushHandler(new StreamHandler(__DIR__ . '/../../logs/usermodel.log', Logger::WARNING));
    }

    public function findUserByEmail($email) {
            $this->db->query("SELECT id FROM users WHERE email = :email");
            $this->db->bind(':email', $email);

            return $this->db->single();
        }

        public function register($name, $email, $password) {
            $this->db->query("INSERT INTO users (name, email, password) VALUES (:name, :email, :password)");
            $this->db->bind(':name', $name);
            $this->db->bind(':email', $email);
            $this->db->bind(':password', $password);

            $executeResult = $this->db->execute(); // Execute the insert query

            if ($executeResult) {
                $userId = $this->db->lastInsertId(); // Retrieve the last inserted ID after successful execution
                return $userId; // Return the user ID if insert was successful
            } else {
                return false; // Return false if insert failed
            }
        }

}