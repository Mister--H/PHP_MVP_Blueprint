<?php
namespace App\Controllers;

use App\Models\User;
use \Firebase\JWT\JWT;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class UserController {
    private $User;
    private $logger;
    private $jwtSecretKey = 'your_secret_key';

    public function __construct(User $User) {
        $this->User = $User;
        
        // Initialize Logger
        $this->logger = new Logger('UserController');
        $this->logger->pushHandler(new StreamHandler(__DIR__.'/../logs/user.log', Logger::DEBUG));
    }

    public function register($name, $email, $password) {
        if ($this->User->findUserByEmail($email)) {
            return ['error' => 'User already exists with this email address.'];
        }

        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        $userId = $this->User->register($name, $email, $passwordHash);
        if ($userId) {
            $payload = [
                "iss" => $_ENV['BASE_URL'],
                "aud" => $_ENV['BASE_URL'],
                "iat" => time(),
                "exp" => time() + (24 * 60 * 60),
                "sub" => $userId,
            ];

            $jwt = JWT::encode($payload, $this->jwtSecretKey, 'HS256');
            $_SESSION['user_id']=$user;
            return ['token' => $jwt];
        } else {
            return ['error' => 'Registration failed.'];
        }
    }





}