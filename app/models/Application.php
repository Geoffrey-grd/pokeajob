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
        $stmt = $this->conn->prepare("SELECT application.*, student.name, student.last_name FROM application JOIN student ON application.id_user = student.id_user WHERE application.id_user = ? AND application.id_offer = ?");
        $stmt->execute([$id_user, $id_offer]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getApplicationsByOffer($id_offer) {
        $stmt = $this->conn->prepare("SELECT application.*, student.name, student.last_name, student.profile_pic_path FROM application JOIN student ON application.id_user = student.id_user WHERE application.id_offer = ? ORDER BY application.application_date DESC");
        $stmt->execute([$id_offer]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getApplicationsByStudents($id_user, $limit = 12, $offset = 0) {
        
            $stmt = $this->conn->prepare("SELECT offer.*, company.id_user,company.company_name, company.banner_path, company.logo_path, application.application_date, application.state FROM offer 
                JOIN company ON offer.id_company = company.id_user
                JOIN application ON offer.id_offer = application.id_offer
                WHERE application.id_user = ?
                ORDER BY application.application_date DESC
                LIMIT ?, ?");

            $stmt->bindValue(1, $id_user, PDO::PARAM_INT);
            $stmt->bindValue(2, $offset, PDO::PARAM_INT);
            $stmt->bindValue(3, $limit, PDO::PARAM_INT);

            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function modifyApplicationStatus($id_application, $state) {
        $stmt = $this->conn->prepare("UPDATE application SET state = ? WHERE id_candidature = ?");
        return $stmt->execute([$state, $id_application]);
    }
}