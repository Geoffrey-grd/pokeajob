<?php

namespace App\Models;

use PDO;

class Company extends User {

    public function __construct() {
        parent::__construct();
    }

    public function create_company($email, $password, $company_name, $address, $sector, $phone, $ciret, $role) {
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->conn->prepare("INSERT INTO user (email, password, role) VALUES (?, ?, ?)");
        $stmt->execute([$email, $passwordHash, $role]);

        $id_user = $this->conn->lastInsertId();

        $stmt = $this->conn->prepare("INSERT INTO company (id_user, company_name, address, id_sector, phone, ciret) VALUES (?, ?, ?, ?, ?, ?)");
        return $stmt->execute([$id_user, $company_name, $address, $sector, $phone, $ciret]);
    }

    public function getCompanyInformations($id_user) {
        $stmt = $this->conn->prepare("SELECT user.email, company.company_name, company.address, activity_sector.sector_name, company.phone, company.ciret, company.description, company.banner_path, company.logo_path, company.rating
            FROM company JOIN user ON company.id_user = user.id_user JOIN activity_sector ON company.id_sector = activity_sector.id_sector WHERE company.id_user = ?");
        $stmt->execute([$id_user]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateCompany($id_user, $logo_path, $banner_path, $company_name, $email, $phone, $description) {
        $stmt = $this->conn->prepare("UPDATE user JOIN company ON user.id_user = company.id_user SET user.email = ?, company.company_name = ?, company.phone = ?, company.description = ?, company.logo_path = ?, company.banner_path = ? WHERE user.id_user = ?");
        return $stmt->execute([$email, $company_name, $phone, $description, $logo_path, $banner_path, $id_user]);
    }

    public function getLogo($user_id) {
        $stmt = $this->conn->prepare("SELECT logo_path FROM company WHERE id_user = ?");
        $stmt->execute([$user_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getBanner($user_id) {
        $stmt = $this->conn->prepare("SELECT banner_path FROM company WHERE id_user = ?");
        $stmt->execute([$user_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getCompanies($limit = 12, $offset = 0) {
        $stmt = $this->conn->prepare("SELECT company_name, banner_path, logo_path FROM company WHERE id_user != ? LIMIT ?, ?");

        $stmt->bindValue(1, $_SESSION["user_id"], PDO::PARAM_INT);
        $stmt->bindValue(2, (int)$offset, PDO::PARAM_INT);
        $stmt->bindValue(3, (int)$limit, PDO::PARAM_INT);
    
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function countCompanies() {
        $stmt = $this->conn->prepare("SELECT COUNT(*) as total FROM company WHERE id_user != ?");
        $stmt->execute([$_SESSION["user_id"]]);
        return $stmt->fetch(PDO::FETCH_ASSOC)["total"];
    }

    public function getFilteredCompanies($limit, $offset, $filter) {
        $stmt = $this->conn->prepare("SELECT company.company_name, company.banner_path, company.logo_path FROM company 
            JOIN activity_sector ON company.id_sector = activity_sector.id_sector
            WHERE company.id_user != ? AND activity_sector.sector_name = ?
            LIMIT ?, ?");
        $stmt->bindValue(1, $_SESSION["user_id"], PDO::PARAM_INT);
        $stmt->bindValue(2, $filter, PDO::PARAM_STR);
        $stmt->bindValue(3, (int)$offset, PDO::PARAM_INT);
        $stmt->bindValue(4, (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function countFilteredCompanies($filter) {
        $stmt = $this->conn->prepare("SELECT COUNT(*) as total FROM company 
            JOIN activity_sector ON company.id_sector = activity_sector.id_sector
            WHERE company.id_user != ? AND activity_sector.sector_name = ?");
        $stmt->execute([$_SESSION["user_id"], $filter]);
        return $stmt->fetch(PDO::FETCH_ASSOC)["total"];
    }

    public function getAllActivitySectors() {
        $stmt = $this->conn->prepare("SELECT sector_name FROM activity_sector");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    
}