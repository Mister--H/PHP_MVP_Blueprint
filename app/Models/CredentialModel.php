<?php
namespace App\Models;

use PDO;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class CredentialModel {
    private $db;
    private $logger;

    public function __construct(Database $db) {
        $this->db = $db;

        // Initialize Monolog logger for UserModel
        $this->logger = new Logger('UserModel');
        $this->logger->pushHandler(new StreamHandler(__DIR__ . '/../../logs/CredentialModel.log', Logger::WARNING));
    }

public function getCredentials($userId) {
        $this->db->query("SELECT * FROM woocommerce_credentials WHERE user_id = :userId");
        $this->db->bind(':userId', $userId);
        return $this->db->resultSet();
    }
    public function findCredBy($storeUrl, $userId) {
        $this->db->query("SELECT user_id FROM woocommerce_credentials WHERE user_id = :userId AND store_url = :storeUrl");
        $this->db->bind(':storeUrl', $storeUrl);
        $this->db->bind(':userId', $userId);
        return $this->db->single();
    }

    public function addCredentials($storeUrl, $consumerKeyHash, $consumerSecretHash, $userId) {
        $this->db->query("INSERT INTO woocommerce_credentials (user_id, store_url, consumer_key, consumer_secret) VALUES (:user_id, :store_url, :consumer_key, :consumer_secret)");
        $this->db->bind(':user_id', $userId);
        $this->db->bind(':store_url', $storeUrl);
        $this->db->bind(':consumer_key', $consumerKeyHash);
        $this->db->bind(':consumer_secret', $consumerSecretHash);

        $executeResult = $this->db->execute(); // Execute the insert query

        if ($executeResult) {
            return true; 
        } else {
            return false; // Return false if insert failed
        }
    }
    
    public function deleteCred($id){
        $this->db->query("DELETE FROM `woocommerce_credentials` WHERE `woocommerce_credentials`.`id` = :id");
        $this->db->bind(':id', $id);
        
        return $this->db->execute();
    }

}