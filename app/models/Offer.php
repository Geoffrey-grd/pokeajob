<?php

namespace App\Models;

use PDO;

class Offer extends BDDlink {

    public function __construct() {
        parent::__construct();
    }

    public function create_offer($id_company, $id_domain, $offer_object, $lieu, $annual_salary, $description) {
        $stmt = $this->conn->prepare("INSERT INTO offer (id_company, id_domain, offer_object, lieu, annual_salary, description) VALUES (?, ?, ?, ?, ?, ?)");
        return $stmt->execute([$id_company, $id_domain, $offer_object, $lieu, $annual_salary, $description]);
    }
    
    public function getAllOffers($limit = 12, $offset = 0) {
        
            $stmt = $this->conn->prepare("SELECT offer.*, company.id_user, company.company_name, company.banner_path, company.logo_path
                FROM offer JOIN company ON offer.id_company = company.id_user
                WHERE offer.id_company != ?
                ORDER BY offer.release_date DESC
                LIMIT ?, ?");

            $stmt->bindValue(1, $_SESSION["user_id"], PDO::PARAM_INT);
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

    public function getOfferById($id_offer) {
        $stmt = $this->conn->prepare("SELECT offer.*, company.company_name, company.banner_path, company.logo_path
            FROM offer JOIN company ON offer.id_company = company.id_user
            WHERE offer.id_offer = ?");
        $stmt->execute([$id_offer]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getOffersByCompany($id_company, $limit = 4, $offset = 0) {
        $stmt = $this->conn->prepare("SELECT offer.*, company.company_name, company.banner_path, company.logo_path
            FROM offer JOIN company ON offer.id_company = company.id_user
            WHERE offer.id_company = ?
            ORDER BY offer.release_date DESC
            LIMIT ?, ?");
            
        $stmt->bindValue(1, $id_company, PDO::PARAM_INT);
        $stmt->bindValue(2, $offset, PDO::PARAM_INT);
        $stmt->bindValue(3, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function countOffersByCompany($id_company) {
        $stmt = $this->conn->prepare("SELECT COUNT(*) as total FROM offer WHERE id_company = ?");
        $stmt->execute([$id_company]);
        return $stmt->fetch(PDO::FETCH_ASSOC)["total"];
    }

    public function getFilteredOffers($limit, $offset, $filter) {
        $stmt = $this->conn->prepare("SELECT offer.*, company.id_user; company.company_name, company.banner_path, company.logo_path FROM offer 
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

    public function getFilteredOffersByCompany($id_company, $limit, $offset, $filter) {
        $stmt = $this->conn->prepare("SELECT offer.*, company.company_name, company.banner_path, company.logo_path FROM offer 
        JOIN company ON offer.id_company = company.id_user
        JOIN domain ON offer.id_domain = domain.id_domain
        WHERE offer.id_company = ? AND domain.domain_name = ?
        LIMIT ?, ?");

        $stmt->bindValue(1, $id_company, PDO::PARAM_INT);
        $stmt->bindValue(2, $filter, PDO::PARAM_STR);
        $stmt->bindValue(3, $offset, PDO::PARAM_INT);
        $stmt->bindValue(4, $limit, PDO::PARAM_INT);

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function countFilteredOffersByCompany($id_company, $filter) {
        $stmt = $this->conn->prepare("SELECT COUNT(*) as total FROM offer 
            JOIN company ON offer.id_company = company.id_user
            JOIN domain ON offer.id_domain = domain.id_domain
            WHERE offer.id_company = ? AND domain.domain_name = ?");
        $stmt->execute([$id_company, $filter]);
        return $stmt->fetch(PDO::FETCH_ASSOC)["total"];
    }

    public function getAllDomains() {
        $stmt = $this->conn->prepare("SELECT id_domain, domain_name FROM domain");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getFromWishlist($user_id, $offer_id) {
        $stmt = $this->conn->prepare("SELECT * FROM favorite_offer WHERE id_user = ? AND id_offer = ?");
        $stmt->execute([$user_id, $offer_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function getWishListByUserId($user_id) {
        $stmt = $this->conn->prepare("SELECT offer.*, company.id_user,company.company_name, company.banner_path, company.logo_path FROM favorite_offer 
            JOIN offer ON favorite_offer.id_offer = offer.id_offer
            JOIN user ON favorite_offer.id_user = user.id_user
            JOIN company ON offer.id_company = company.id_user
            WHERE favorite_offer.id_user = ?");
        $stmt->execute([$user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getOfferIdinWishlist($user_id) {
        $stmt = $this->conn->prepare("SELECT id_offer FROM favorite_offer WHERE id_user = ?");
        $stmt->execute([$user_id]);
        return $stmt->fetchall(PDO::FETCH_ASSOC);
    }


    public function addToWishList($user_id, $offer_id) {
        $stmt = $this->conn->prepare("INSERT INTO favorite_offer (id_user, id_offer) VALUES (?, ?)");
        return $stmt->execute([$user_id, $offer_id]);
    }

    public function removeFromWishList($user_id, $offer_id) {
        $stmt = $this->conn->prepare("DELETE FROM favorite_offer WHERE id_user = ? AND id_offer = ?");
        return $stmt->execute([$user_id, $offer_id]);
    }

    public function editOffer($id_offer, $id_domain, $offer_object, $lieu, $annual_salary, $description) {
        $stmt = $this->conn->prepare("UPDATE offer SET id_domain = ?, offer_object = ?, lieu = ?, annual_salary = ?, description = ? WHERE id_offer = ?");
        return $stmt->execute([$id_domain, $offer_object, $lieu, $annual_salary, $description, $id_offer]);
    }

    public function deleteOffer($id_offer) {
        $stmt = $this->conn->prepare("DELETE FROM offer WHERE id_offer = ?");
        return $stmt->execute([$id_offer]);
    }



}