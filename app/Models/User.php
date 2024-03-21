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

        public function verifyUserCredentials($email, $password) {
            $this->db->query("SELECT * FROM users WHERE email = :email");
            $this->db->bind(':email', $email);
            $user = $this->db->single();

            if ($user && password_verify($password, $user['password'])) {
                return $user; // Return the user data if credentials are valid
            } else {
                return false; // Return false if credentials are invalid
            }
        }
        public function storeRememberToken($userId, $token) {
            $hashedToken = password_hash($token, PASSWORD_DEFAULT);
            $expiresAt = date('Y-m-d H:i:s', strtotime('+30 days')); // Token expires in 30 days

            $this->db->query("INSERT INTO remember_tokens (user_id, token, expires_at) VALUES (:user_id, :token, :expires_at)");
            $this->db->bind(':user_id', $userId);
            $this->db->bind(':token', $hashedToken);
            $this->db->bind(':expires_at', $expiresAt);
            $this->db->execute();
        }


        public function validateRememberToken($token) {
            $this->db->query("SELECT user_id, token FROM remember_tokens WHERE expires_at > NOW()");
            $tokens = $this->db->resultSet();

            foreach ($tokens as $tokenRow) {
                if (password_verify($token, $tokenRow['token'])) {
                    return $tokenRow['user_id']; // Return user ID if token is valid
                }
            }

            return false;
        }


        public function getUserById($userId) {
            $this->db->query("SELECT * FROM users WHERE id = :userId");
            $this->db->bind(':userId', $userId);
            return $this->db->single();
        }




}