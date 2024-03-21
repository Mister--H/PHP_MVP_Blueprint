<?php

namespace App\Controllers;

use App\Models\User;
use App\Models\WooModel; // Assuming this model exists and can fetch credentials
use App\Models\Database;

class PageController {
    private $wooModel;
    private $userId;

    public function __construct() {
        $database = new Database();
        $this->wooModel = new WooModel($database);
        $this->userId = $_SESSION['user_id']['id'] ?? null;
    }

    public function home() {
        renderView('home');
    }

    public function dashboard() {
        renderView('dashboard/index');
    }

}

