<?php
namespace App\Controllers;

use App\Models\Database;
use App\Models\CredentialModel;
use App\Models\SettingModel;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class SettingController {
    private $CredentialModel;
    private $SettingModel;
    private $logger;
    private $userId;
    
    public function __construct() {
        $db = new Database();
        $this->CredentialModel = new CredentialModel($db);
        $this->SettingModel = new SettingModel($db);
        $this->userId = $_SESSION['user_id']['id'] ?? null;
        
        // Initialize Logger
        $this->logger = new Logger('WooController');
        $this->logger->pushHandler(new StreamHandler(__DIR__.'/../logs/woo.log', Logger::DEBUG));
    }

 public function showSettings() {
        $credentials = $this->CredentialModel->getCredentials($this->userId);
        $data = ['credentials'=> $credentials];
        renderView('dashboard/settings', $data);
    }

    public function newSetting(){
        $storeId = $_POST['store_id'] ?? ''; // Use the null coalescing operator to provide a default value
        $name = $_POST['name'] ?? '';
        $title = $_POST['title'] ?? '';
        $category = $_POST['category'] ?? '';
        $price = $_POST['price'] ?? '';
        $offPrice = $_POST['off_price'] ?? '';
        $image = $_POST['image'] ?? '';
        $gallery = $_POST['gallery'] ?? '';
        $attributeContainer = $_POST['attribute_container'] ?? '';
        $attributeLabel = $_POST['attribute_label'] ?? '';
        $attributeValue = $_POST['attribute_value'] ?? '';

        $result = $this->SettingModel->newSetting($title, $category, $price, $offPrice,$image,$gallery,$attributeContainer,$attributeLabel,$attributeValue,$storeId,$this->userId);
        
        if($result){
            $this->showSettings();
        }
    }

}