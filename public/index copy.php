<?php
// Improved error reporting setup
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../app/bootstrap.php';

use App\Controllers\UserController;
use App\Controllers\WooController;
use App\Models\User;
use App\Models\Woo;
use App\Models\Database;


function handleRequest() {
    $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $pathSegments = explode('/', trim($path, '/'));

    // Establish database connection and user context outside conditional blocks
    $db = new Database();
    $userController = new UserController(new User($db));
    $userId = $_SESSION['user_id']['id'] ?? null;  // Handle potential missing session

    if ($userController->isLoggedIn()) {
        if ($pathSegments[0] === 'dashboard') {
            switch ($pathSegments[1] ?? null) {
                case 'credentials':
                    $action = $pathSegments[2] ?? null;
                    $id = $pathSegments[3] ?? null;

                    if ($action === 'edit') {
                        handleEditCred($id);
                    } elseif ($action === 'delete') {
                        handleDeleteCred($id);
                    } else {
                        handleCred();
                    }
                    break;
                case 'woo':
                    // Handle other dashboard routes (e.g., 'woo/settings', 'woo/add-product', etc.)
                    handleWooRoutes($pathSegments);
                    break;
                default:
                    renderView('dashboard/index'); // Default for other dashboard routes
            }
        }
    } else {
        switch ($pathSegments[0]) {
            case 'login':
                handleLogin();
                break;
            case 'register':
                renderView('auth/register');
                break;
            case 'dashboard':
                header('Location: /login');
                break;
            default:
                renderView('404'); // Ensure a 404 view exists
                break;
        }
    }
}

function handleWooRoutes($pathSegments) {
    switch ($pathSegments[2] ?? null) {
        case 'settings':
            handleWooSettings();
            break;
        case 'add-product':
            handleAddProduct();
            break;
        case 'add-content':
            handleAddContent();
            break;
        default:
            renderView('dashboard/woocommerce/woo'); // Default for other 'woo' routes
    }
}

// Call the main function
handleRequest();

function handleRegister() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        processRegistration();
    } else {
          $db = getDatabaseConnection(); 
        $userController = new UserController(new User($db));
        if ($userController->isLoggedIn()) {
            header('Location: /dashboard/');
            exit;
        }
        renderView('auth/register'); // Show registration form for GET requests
    }
}

function processRegistration() {
    $db = getDatabaseConnection(); 
    $userController = new UserController(new User($db));

    $name = sanitizeInput($_POST['name'] ?? '');
    $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password']; 

    $result = $userController->register($name, $email, $password);

    if (isset($result['error'])) {
       
        $_SESSION['error'] = $result['error']; // Store the error message in the session
        header('Location: /register');
        exit;
    } else {
        // Redirect to the login page after successful registration
        header('Location: /login');
    }
    exit;
}

function handleLogin() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        processLogin();
    } else {
        $db = getDatabaseConnection(); 
        $userController = new UserController(new User($db));
        if ($userController->isLoggedIn()) {
            header('Location: /dashboard');
            exit;
        } else {
            renderView('auth/login');
        }
    }
}

function processLogin() {
    $db = getDatabaseConnection(); 
    $userController = new UserController(new User($db));

    $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];
    $rememberMe = isset($_POST['remember_me']);

    $result = $userController->login($email, $password, $rememberMe);

    if (isset($result['error'])) {
        $_SESSION['error'] = $result['error']; // Store the error message in the session
        header('Location: /login');
    } else {
        // Assuming you're storing the token in a secure, HttpOnly cookie
        setcookie('token', $result['token'], ['httponly' => true, 'samesite' => 'Strict']);
        // Redirect to the dashboard page after successful login
        header('Location: /dashboard');
    }
    exit;
}

function handleCred() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        processAddingCred();
    } else {
        $db = getDatabaseConnection(); 
        $userController = new UserController(new User($db));
        $wooController = new WooController(new Woo($db));
        $userId = $_SESSION['user_id']['id'];

        if ($userController->isLoggedIn()) {
            // Assuming you fetch multiple credentials from the database
            //[ ['store_url' => 'https://example.com', 'consumer_key' => 'ck_xxx', 'consumer_secret' => 'cs_yyy'],];
            $credentials = $wooController->getCredentials($userId);
                // Add more credentials as fetched from the database
            $data = ['credentials'=> $credentials];
            renderView('dashboard/woocommerce/cred', $data);
            exit;
        } else {
            header('Location: /login');
        }
    }
}

function processAddingCred() {
    $db = getDatabaseConnection(); 
    $wooController = new WooController(new Woo($db));

    $storeUrl = filter_var($_POST['storeURL'] ?? '', FILTER_SANITIZE_URL);
    $consumerKey = $_POST['consumerKey'];
    $consumerSecret = $_POST['consumerSecret'];
    $userId = $_SESSION['user_id']['id'];

    $result = $wooController->addCred($storeUrl, $consumerKey, $consumerSecret, $userId);

    if (isset($result['error'])) {
        $_SESSION['error'] = $result['error']; // Store the error message in the session
        header('Location: /dashboard/credentials');
    } else {
        header('Location: /dashboard/credentials');
    }
    exit;
}

function handleDeleteCred($id) {
    $db = getDatabaseConnection(); 
    $wooController = new WooController(new Woo($db));
    $result = $wooController->deleteCred($id);
    if($result){
        header('Location: /dashboard/credentials');
    }
}

function handleWoo() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        processAddingCred();
    } else {
        $db = getDatabaseConnection(); 
        $userController = new UserController(new User($db));
        $wooController = new WooController(new Woo($db));
        $userId = $_SESSION['user_id']['id'];

        if ($userController->isLoggedIn()) {
            // Assuming you fetch multiple credentials from the database
            //[ ['store_url' => 'https://example.com', 'consumer_key' => 'ck_xxx', 'consumer_secret' => 'cs_yyy'],];
            $credentials = $wooController->getCredentials($userId);
                // Add more credentials as fetched from the database
            $data = ['credentials'=> $credentials];
            renderView('dashboard/woocommerce/woo', $data);
            exit;
        } else {
            header('Location: /login');
        }
    }
}

function handleWooSettings() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        processWooSettings();
    } else {
        $db = getDatabaseConnection(); 
        $userController = new UserController(new User($db));
        $wooController = new WooController(new Woo($db));
        $userId = $_SESSION['user_id']['id'];

        if ($userController->isLoggedIn()) {
            $credentials = $wooController->getCredentials($userId);
            $data = ['credentials'=> $credentials];
            renderView('dashboard/woocommerce/settings', $data);
            exit;
        } else {
            header('Location: /login');
        }
    }
}

function processWooSettings(){
    $db = getDatabaseConnection(); 
    $wooController = new WooController(new Woo($db));
    $userId = $_SESSION['user_id']['id'];

    $title = sanitizeInput($_POST['title'] ?? '');
    $category = sanitizeInput($_POST['category'] ?? '');
    $price = sanitizeInput($_POST['price'] ?? '');
    $offPrice = sanitizeInput($_POST['off_price'] ?? '');
    $image = sanitizeInput($_POST['image'] ?? '');
    $gallery = sanitizeInput($_POST['gallery'] ?? '');
    $attributeContainer = sanitizeInput($_POST['attribute_container'] ?? '');
    $attributeLabel = sanitizeInput($_POST['attribute_label'] ?? '');
    $attributeValue = sanitizeInput($_POST['attribute_value'] ?? '');
    $storeId = sanitizeInput($_POST['store_id'] ?? '');

    

    $result = $wooController->newSetting($title, $category, $price, $offPrice,$image,$gallery,$attributeContainer,$attributeLabel,$attributeValue,$storeId, $userId);

    if (isset($result['error'])) {
       
        $_SESSION['error'] = $result['error']; // Store the error message in the session
        header('Location: /dashboard/woo/settings');
        exit;
    } else {
        // Redirect to the login page after successful registration
        header('Location: /dashboard/woo/settings');
    }
    exit;
}

function handleAddProduct() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        processAddProduct();
    } else {
           $db = getDatabaseConnection(); 
        $userController = new UserController(new User($db));
        $wooController = new WooController(new Woo($db));
        $userId = $_SESSION['user_id']['id'];

        if ($userController->isLoggedIn()) {
            $credentials = $wooController->getCredentials($userId);
            $data = ['credentials'=> $credentials, "result" => ''];
             renderView('dashboard/woocommerce/addProduct', $data);
            exit;
        }
        renderView('auth/login'); // Show registration form for GET requests
    }
}

function processAddProduct() {
    $db = getDatabaseConnection(); 
    $wooController = new WooController(new Woo($db));
    $userId = $_SESSION['user_id']['id'];

    $store = sanitizeInput($_POST['store_id'] ?? '');
    $url = sanitizeInput($_POST['product_url'] ?? '');
    $haveContent = isset($_POST['have_content']);
    $needApproval =  isset($_POST['need_approval']);
    $sku = sanitizeInput($_POST['sku'] ?? '');
    $brand = sanitizeInput($_POST['brand'] ?? '');
    $price = sanitizeInput($_POST['price'] ?? '');

    $result = $wooController->newProduct($store, $url, $haveContent, $needApproval, $sku, $brand, $price, $userId);
    $credentials = $wooController->getCredentials($userId);
    $data = ['credentials'=> $credentials, 'result'=> $result];
    if (isset($result['error'])) {
       
        $_SESSION['error'] = $result['error']; // Store the error message in the session
        header('Location: /dashboard/woo/add-product');
        exit;
    } else {
        // Redirect to the login page after successful registration
         renderView('dashboard/woocommerce/addProduct', $data);
    }
    exit;
}

function handleAddContent() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        processAddContent();
    } else {
           $db = getDatabaseConnection(); 
        $userController = new UserController(new User($db));
        $wooController = new WooController(new Woo($db));
        $userId = $_SESSION['user_id']['id'];

        if ($userController->isLoggedIn()) {
            $credentials = $wooController->getCredentials($userId);
            $data = ['credentials'=> $credentials];
             renderView('dashboard/woocommerce/addContent', $data);
            exit;
        }
        renderView('auth/login'); // Show registration form for GET requests
    }
}

function processAddContent() {
    $db = getDatabaseConnection(); 
    $wooController = new WooController(new Woo($db));
    $userId = $_SESSION['user_id']['id'];

    $store = sanitizeInput($_POST['store_id'] ?? '');
    $url = sanitizeInput($_POST['product_url'] ?? '');
    $sku = sanitizeInput($_POST['sku'] ?? '');
    $content = sanitizeInput($_POST['product_content'] ?? '');

    $result = $wooController->newContent($store, $url, $sku, $content, $userId);

    if (isset($result['error'])) {
       
        $_SESSION['error'] = $result['error']; // Store the error message in the session
        header('Location: /dashboard/woo/add-content');
        exit;
    } else {
        // Redirect to the login page after successful registration
        header('Location: /dashboard/woo/add-content');
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
