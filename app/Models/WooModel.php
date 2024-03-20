<?php
namespace App\Models;

use PDO;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class WooModel {
    private $db;
    private $logger;

    public function __construct(Database $db) {
        $this->db = $db;

        // Initialize Monolog logger for UserModel
        $this->logger = new Logger('WooModel');
        $this->logger->pushHandler(new StreamHandler(__DIR__ . '/../../logs/WooModel.log', Logger::WARNING));
    }
    
    public function newProduct($store, $url, $haveContent, $needApproval, $sku, $brand, $price, $userId) {
        $this->db->query("INSERT INTO scraped_urls (user_id, store_id, product_url, status) VALUES (:user_id, :store_id, :product_url, published)");
        $this->db->bind(':user_id', $userId);
        $this->db->bind(':store_id', $store);
        $this->db->bind(':product_url', $url);

        $scrapedId = $this->db->lastInsertId();
        
        $this->db->query("INSERT INTO scraped_details (scraped_id, detail_key, detail_value) VALUES (:scraped_id, :detail_key, :detail_value)");
        $this->db->bind(':scraped_id', $scrapedId);
        $this->db->bind(':store_id', $store);
        $this->db->bind(':product_url', $url);

        return $this->db->execute();

    }

}