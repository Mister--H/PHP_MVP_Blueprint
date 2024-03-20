<?php
namespace App\Models;

use PDO;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class ContentModel {
    private $db;
    private $logger;

    public function __construct(Database $db) {
        $this->db = $db;

        // Initialize Monolog logger for UserModel
        $this->logger = new Logger('ContentModel');
        $this->logger->pushHandler(new StreamHandler(__DIR__ . '/../../logs/ContentModel.log', Logger::WARNING));
    }

    public function newContent($store, $url, $sku, $content, $userId) {
        $this->db->query("INSERT INTO product_content (user_id, store_id, product_url, product_content, product_model) VALUES (:user_id, :store_id, :product_url, :product_content, :product_model)");
        $this->db->bind(':user_id', $userId);
        $this->db->bind(':store_id', $store);
        $this->db->bind(':product_url', $url);
        $this->db->bind(':product_content', $content);
        $this->db->bind(':product_model', $sku);

        return $this->db->execute();

    }

    public function getContent($productUrl){
        $this->db->query("SELECT product_content FROM product_content WHERE product_url = :productURL");
        $this->db->bind(':productURL', $productUrl);
        
        return $this->db->single();

    }

}