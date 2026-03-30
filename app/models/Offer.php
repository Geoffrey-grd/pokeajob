<?php

namespace App\Models;

use PDO;

class Offer extends BDDlink {

    public function __construct() {
        parent::__construct();
    }
    
    public function create_offer($id_company, $id_domain, $offer_object, $lieu, $description, ) {
        $stmt = $this->conn->prepare("INSERT INTO offer (id_company, id_domain, offer_object, lieu, description) VALUES (?, ?, ?, ?, ?)");
        return $stmt->execute([$id_company, $id_domain, $offer_object, $lieu, $description]);
    }
    
    public function getAllOffers($limit = 12, $offset = 0) {
        
            $stmt = $this->conn->prepare("SELECT offer.*, company.company_name, company.banner_path, company.logo_path
                FROM offer JOIN company ON offer.id_company = company.id_user
                WHERE offer.id_company != ?
                ORDER BY offer.release_date ASC
                LIMIT ?, ?");

            $stmt->bindValue(1  , $_SESSION["user_id"], PDO::PARAM_INT);
            $stmt->bindValue(2, $offset, PDO::PARAM_INT);
            $stmt->bindValue(3, $limit, PDO::PARAM_INT);

            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function countOffers() {
        $stmt = $this->conn->prepare("SELECT COUNT(*) as total FROM offer WHERE id_company != ?");
        $stmt->execute([$_SESSION["user_id"]]);
        return $stmt->fetch(PDO::FETCH_ASSOC)["total"];
    }

    public function getFilteredOffers($limit, $offset, $filter) {
        $stmt = $this->conn->prepare("SELECT offer.*, company.company_name, company.banner_path, company.logo_path FROM offer 
        JOIN company ON offer.id_company = company.id_user
        JOIN domain ON offer.id_domain = domain.id_domain
        WHERE company.id_user != ? AND domain.domain_name = ?
        LIMIT ?, ?");

        $stmt->bindValue(1, $_SESSION["user_id"], PDO::PARAM_INT);
        $stmt->bindValue(2, $filter, PDO::PARAM_STR);
        $stmt->bindValue(3, $offset, PDO::PARAM_INT);
        $stmt->bindValue(4, $limit, PDO::PARAM_INT);

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function countFilteredOffers($filter) {
        $stmt = $this->conn->prepare("SELECT COUNT(*) as total FROM offer 
            JOIN company ON offer.id_company = company.id_user
            JOIN domain ON offer.id_domain = domain.id_domain
            WHERE company.id_user != ? AND domain.domain_name = ?");
        $stmt->execute([$_SESSION["user_id"], $filter]);
        return $stmt->fetch(PDO::FETCH_ASSOC)["total"];
    }

    public function getAllDomains() {
        $stmt = $this->conn->prepare("SELECT id_domain, domain_name FROM domain");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }



}