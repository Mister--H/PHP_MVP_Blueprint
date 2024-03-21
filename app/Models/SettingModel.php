<?php
namespace App\Models;

use PDO;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class SettingModel {
    private $db;
    private $logger;

    public function __construct(Database $db) {
        $this->db = $db;

        // Initialize Monolog logger for UserModel
        $this->logger = new Logger('SettingModel');
        $this->logger->pushHandler(new StreamHandler(__DIR__ . '/../../logs/SettingModel.log', Logger::WARNING));
    }

    public function newSetting($title, $category, $price, $offPrice, $image, $gallery, $attributeContainer, $attributeLabel, $attributeValue, $storeId, $userId) {
        $options = [
            'title' => $title,
            'category' => $category,
            'price' => $price,
            'offPrice' => $offPrice,
            'image' => $image,
            'gallery' => $gallery,
            'attributeContainer' => $attributeContainer,
            'attributeLabel' => $attributeLabel,
            'attributeValue' => $attributeValue,
        ];

        $executeResult = true;
        foreach ($options as $optionKey => $optionValue) {
            $this->db->query("INSERT INTO options (user_id, store_id, option_key, option_value) VALUES (:user_id, :store_id, :option_key, :option_value)");
            $this->db->bind(':user_id', $userId);
            $this->db->bind(':store_id', $storeId);
            $this->db->bind(':option_key', $optionKey);
            $this->db->bind(':option_value', $optionValue);
            
            if (!$this->db->execute()) {
                $executeResult = false;
                break; // Exit the loop if any insert fails
            }
        }

        return $executeResult;
    }

    public function getWooSettings($store, $userId){
        $this->db->query("SELECT * FROM options WHERE store_id = :storeId AND user_id = :userId");
        $this->db->bind(':storeId', $store);
        $this->db->bind(':userId', $userId);
        $result = $this->db->resultSet();

        $settings = [];
        foreach ($result as $item) {
            $settings[$item['option_key']] = $item['option_value'];
        }

        $this->db->query("SELECT * FROM woocommerce_credentials WHERE user_id = :userId");
        $this->db->bind(':userId', $userId);
        $result = $this->db->resultSet();

        $settings['store_url'] = $result[0]['store_url'];
        $settings['consumer_secret'] = $result[0]['consumer_secret'];
        $settings['consumer_key'] = $result[0]['consumer_key'];
        
        return $settings;
    }

}