<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../app/bootstrap.php';

use App\Controllers\UserController;
use App\Models\User;
use App\Models\Database; // Assuming you have a Database class to instantiate $db

$db = new Database(); // Make sure this is correctly set up to return a database connection

// Example routing logic
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH); // Get the current path requested by the user

// Basic routing
switch ($path) {
    case '/':
        renderView('home');
        break;
    case '/register':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userModel = new User($db); // Ensure $db is properly instantiated
            $userController = new UserController($userModel);
            
            $name = trim(htmlspecialchars($_POST['name'] ?? '', ENT_QUOTES, 'UTF-8'));
            $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
            $password = $_POST['password']; // Ensure this is sanitized and validated

            $result = $userController->register($name, $email, $password);

            if (isset($result['error'])) {
                // Handle error, perhaps redirect back to form with an error message
                echo $result['error']; // Consider a more secure way to display errors
            } else {
                // Handle success, perhaps redirecting to a login page or showing a success message
                echo 'Registration successful. Token: ' . $result['token']; // Consider a more secure way to handle token
            }

            exit; // Prevent further processing
        } else {
            // GET request: Show the registration form
            renderView('auth/register');
        }
        break;
    default:
        // 404 page or redirect to home
        renderView('404'); // Make sure you have a 404 view
        break;
}
