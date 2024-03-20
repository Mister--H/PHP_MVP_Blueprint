<?php
namespace App\Controllers;

use App\Models\Database;
use App\Models\CredentialModel;
use App\Models\ContentModel;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class ContentController {
    private $CredentialModel;
    private $ContentModel;
    private $logger;
    private $userId;
    
    public function __construct() {
        $db = new Database();
        $this->CredentialModel = new CredentialModel($db);
        $this->ContentModel = new ContentModel($db);
        $this->userId = $_SESSION['user_id']['id'] ?? null;
        
        // Initialize Logger
        $this->logger = new Logger('ContentController');
        $this->logger->pushHandler(new StreamHandler(__DIR__.'/../logs/ContentController.log', Logger::DEBUG));
    }

    public function showStoreContent(){
        $credentials = $this->CredentialModel->getCredentials($this->userId);
        $data = ['credentials'=> $credentials];
        renderView('dashboard/addContent', $data);
    }

    public function storeContent(){
        $store = sanitizeInput($_POST['store_id'] ?? '');
        $url = sanitizeInput($_POST['product_url'] ?? '');
        $sku = sanitizeInput($_POST['sku'] ?? '');
        $content = sanitizeInput($_POST['product_content'] ?? '');

        $result = $this->ContentModel->newContent($store, $url, $sku, $content, $this->userId);
        if($result){
            $this->showStoreContent();
        }
    }

}