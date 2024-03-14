<?php

require_once __DIR__ . '/../vendor/autoload.php'; 

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

require_once __DIR__ . '/helpers.php'; // Include the helpers file

// Now you can access your environment variables using getenv('VAR_NAME') or $_ENV['VAR_NAME']
