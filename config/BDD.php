<?php
namespace App\Config;

use PDO;
use PDOException;

class BDD {

    private $host;
    private $db_name;
    private $username;
    private $password;

    public $conn;

    public function __construct()
    {
        // Charger la configuration externe
        $config = require __DIR__ . '/config.php';

        $this->host     = $config['host'];
        $this->db_name  = $config['db_name'];
        $this->username = $config['username'];
        $this->password = $config['password'];
    }

    public function connect() {

        $this->conn = null;

        try {
            $this->conn = new PDO(
                "mysql:host={$this->host};dbname={$this->db_name};charset=utf8mb4",
                $this->username,
                $this->password
            );

            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        } catch (PDOException $exception) {
            echo "Erreur connexion : " . $exception->getMessage();
        }

        return $this->conn;
    }
}