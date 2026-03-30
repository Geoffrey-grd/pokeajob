<?php
namespace App\Models;

use App\Config\BDD;

class BDDlink {

    protected $conn;

    public function __construct() {
        $bdd = new BDD();
        $this->conn = $bdd->connect();
    }
}