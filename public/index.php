<?php
// Improved error reporting setup
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../app/bootstrap.php';

use App\Controllers\UserController;
use App\Models\User;
use App\Models\Database;

$db = new Database();

// Simplify routing and handling logic
handleRequest();

function handleRequest() {
    $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

    switch ($path) {
        case '/':
            renderView('home');
            break;
        case '/register':
            handleRegister();
            break;
        default:
            renderView('404'); // Ensure a 404 view exists
    }
}

function handleRegister() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        processRegistration();
    } else {
        renderView('auth/register'); // Show registration form for GET requests
    }
}

function processRegistration() {
    $db = getDatabaseConnection(); // Ensure database connection
    $userController = new UserController(new User($db));

    // Sanitize and validate inputs
    $name = sanitizeInput($_POST['name'] ?? '');
    $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password']; // Consider more robust sanitation/validation

    $result = $userController->register($name, $email, $password);

    // Handle registration result
    if (isset($result['error'])) {
        echo $result['error']; // Redirect or display error securely
    } else {
        echo 'Registration successful. Token: ' . $result['token']; // Secure token handling
    }
    exit;
}

function sanitizeInput($input) {
    return trim(htmlspecialchars($input, ENT_QUOTES, 'UTF-8'));
}

function getDatabaseConnection() {
    // Assuming Database class exists and returns a PDO connection
    return new Database();
}

function renderView($view) {
    // Assuming this function exists to include view files
    include "../views/{$view}.php";
}
