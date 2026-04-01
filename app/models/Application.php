<?php

namespace App\Models;

use PDO;

class Application extends BDDlink {

    public function __construct() {
        parent::__construct();
    }    
    
    public function applyToOffer($id_user, $id_offer, $cv_path, $motivation_letter_path) {
        $stmt = $this->conn->prepare("INSERT INTO application (id_user, id_offer, cv_path, motivation_letter_path) VALUES (?, ?, ?, ?)");
        return $stmt->execute([$id_user, $id_offer, $cv_path, $motivation_letter_path]);
    }

    public function getApplication($id_user, $id_offer) {
        $stmt = $this->conn->prepare("SELECT* FROM application WHERE id_user = ? AND id_offer = ?");
        $stmt->execute([$id_user, $id_offer]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getApplicationsByStudents($id_user, $limit = 12, $offset = 0) {
        
            $stmt = $this->conn->prepare("SELECT offer.*, company.company_name, company.banner_path, company.logo_path, application.application_date FROM offer 
                JOIN company ON offer.id_company = company.id_user
                JOIN application ON offer.id_offer = application.id_offer
                WHERE application.id_user = ?
                ORDER BY application.application_date DESC
                LIMIT ?, ?");

            $stmt->bindValue(1, $_SESSION["user_id"], PDO::PARAM_INT);
            $stmt->bindValue(2, $offset, PDO::PARAM_INT);
            $stmt->bindValue(3, $limit, PDO::PARAM_INT);

            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function countApplicationsByStudents($id_user) {
        $stmt = $this->conn->prepare("SELECT COUNT(*) as total FROM application WHERE id_user = ?");
        $stmt->execute([$id_user]);
        return $stmt->fetch(PDO::FETCH_ASSOC)["total"];
    }
}