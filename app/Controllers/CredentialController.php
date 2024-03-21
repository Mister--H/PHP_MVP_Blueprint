<?php
namespace App\Controllers;

use App\Models\User;
use App\Models\CredentialModel; // Assuming this model exists and can fetch credentials
use App\Models\Database;

class CredentialController {
    private $CredentialModel;
    private $userId;

    public function __construct() {
        $database = new Database();
        $this->CredentialModel = new CredentialModel($database);
        $this->userId = $_SESSION['user_id']['id'] ?? null;
    }

 public function showCredentials() {
        $credentials = $this->CredentialModel->getCredentials($this->userId);
        renderView('dashboard/credentials', ['credentials' => $credentials]);
    }
    public function addCredentials() {
        $storeUrl = filter_var($_POST['storeURL'] ?? '', FILTER_SANITIZE_URL);
        $consumerKey = $_POST['consumerKey'];
        $consumerSecret = $_POST['consumerSecret'];

        if ($this->CredentialModel->findCredBy($storeUrl, $this->userId)) {
            $_SESSION['error'] = 'You already have this store credentials';
            header('Location: /dashboard/credentials');
            exit;
        }

        $consumerKeyHash = encryptCredential($consumerKey, getenv('SECRET_KEY'));
        $consumerSecretHash = encryptCredential($consumerSecret, getenv('SECRET_KEY'));

        $keyId = $this->CredentialModel->addCredentials($storeUrl, $consumerKeyHash, $consumerSecretHash, $this->userId);

        if ($keyId) {
            header('Location: /dashboard/credentials');
        } else {
           $_SESSION['error'] = 'Failed to add';
            header('Location: /dashboard/credentials');
            exit;
        }
        }

        public function deleteCredentials($credentialId){
            $resutlt = $this->CredentialModel->deleteCred($credentialId);   
            if($resutlt){
                header('Location: /dashboard/credentials');
                exit;
            }
        }

    }