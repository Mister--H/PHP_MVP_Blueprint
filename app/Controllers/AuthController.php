<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Models\User;
use App\Models\Database;

class AuthController {
    protected $auth;

    public function __construct() {
        // Assuming Database class takes care of its own configuration
        $database = new Database();
        $user = new User($database);
        $this->auth = new Auth($user);
    }

    public function showLoginForm() {
        if ($this->auth->isLoggedIn()) {
            header('Location: /dashboard');
            exit;
        }
        renderView('auth/login');
    }

    public function processLogin() {
        $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
        $password = $_POST['password'];
        $rememberMe = isset($_POST['remember_me']);

        $result = $this->auth->login($email, $password, $rememberMe);

        if (isset($result['error'])) {
            $_SESSION['error'] = $result['error'];
            header('Location: /login');
            exit;
        }

        setcookie('token', $result['token'], ['httponly' => true, 'samesite' => 'Strict']);
        header('Location: /dashboard');
        exit;
    }
}
