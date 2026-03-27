<?php

namespace App\Models;

use PDO;

class Sector extends BDDlink {

    public function __construct() {
        parent::__construct();
    }
    
    public function getAllSectors() {
        $stmt = $this->conn->prepare("SELECT * FROM activity_sector");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

    public function getidsector($sector) {
        $stmt = $this->conn->prepare("SELECT id_sector FROM activity_sector WHERE sector_name = ?");
        $stmt->execute([$sector]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}