<?php

namespace App\Models;

use PDO;

class Application extends BDDlink {

    public function __construct() {
        parent::__construct();
    }    
    
    public function applyToOffer($id_user, $id_offer, $cv_path, $motivation_letter_path) {
        // var_dump($cv_path, $motivation_letter_path);
        // die();
        $stmt = $this->conn->prepare("INSERT INTO application (id_user, id_offer, cv_path, motivation_letter_path) VALUES (?, ?, ?, ?)");
        return $stmt->execute([$id_user, $id_offer, $cv_path, $motivation_letter_path]);
    }

    // public function getApplication($id_user, $id_offer) {
    //     $stmt = $this->conn->prepare("SELECT* FROM application WHERE id_user = ? AND id_offer = ?");
    //     $stmt->execute([$id_user, $id_offer]);
    //     return $stmt->fetch(PDO::FETCH_ASSOC);
    // }
}