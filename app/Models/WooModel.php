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

    public function addSimilarCategory($storeId, $userId, $sourceCategory, $targetCategory) {
        $this->db->query("INSERT INTO options (store_id, user_id, option_key, option_value, option_type) VALUES (:store_id, :user_id, :option_key, :option_value, 'category')");
        $this->db->bind(':store_id', $storeId);
        $this->db->bind(':user_id', $userId);
        $this->db->bind(':option_key', $sourceCategory);
        $this->db->bind(':option_value', $targetCategory);
        return $this->db->execute();
    }

    public function getTargetCategory($storeId, $userId, $sourceCategory) {
        $this->db->query("SELECT option_value FROM options WHERE store_id = :store_id AND user_id = :user_id AND option_key = :option_key AND option_type = 'category_mapping'");
        $this->db->bind(':store_id', $storeId);
        $this->db->bind(':user_id', $userId);
        $this->db->bind(':option_key', $sourceCategory);
        $result = $this->db->single();
        return $result ? $result['option_value'] : null;
    }

    public function newProduct($store, $url, $userId) {
        $this->db->query("INSERT INTO scraped_urls (user_id, store_id, url, status) VALUES (:user_id, :store_id, :product_url, 'published')");
        $this->db->bind(':user_id', $userId);
        $this->db->bind(':store_id', $store);
        $this->db->bind(':product_url', $url);

        return $this->db->execute();

    }

}