<?php

namespace App\Models;

require_once __DIR__ . "/../../config/database.php";

class Company extends User {

    public static function create_company($conn, $email, $password, $company_name, $address, $sector, $phone, $ciret, $role) {
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("INSERT INTO user (email, password, role) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $email, $passwordHash, $role);
        $stmt->execute();

        $id_user = $conn->insert_id;

        $stmt = $conn->prepare("INSERT INTO company (id_user, company_name, address, id_sector, phone, ciret) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ississ", $id_user, $company_name, $address, $sector, $phone, $ciret);
        return $stmt->execute();
    }

    public static function getCompanyInformations($conn, $id_user) {
        $stmt = $conn->prepare("SELECT user.email, company.company_name, company.address, activity_sector.sector_name, company.phone, company.ciret, company.description, company.banner_path, company.logo_path, company.rating
            FROM company JOIN user ON company.id_user = user.id_user JOIN activity_sector ON company.id_sector = activity_sector.id_sector WHERE company.id_user = ?");
        $stmt->bind_param("i", $id_user);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public static function updateCompany($conn, $id_user, $logo_path, $banner_path, $company_name, $email, $phone, $description) {
        $stmt = $conn->prepare("UPDATE user JOIN company ON user.id_user = company.id_user SET user.email = ?, company.company_name = ?, company.phone = ?, company.description = ?, company.logo_path = ?, company.banner_path = ? WHERE user.id_user = ?");
        $stmt->bind_param("ssssssi", $email, $company_name, $phone, $description, $logo_path, $banner_path, $id_user);
        return $stmt->execute();
    }

    public static function getLogo($conn, $user_id) {
        $stmt = $conn->prepare("SELECT logo_path FROM company WHERE id_user = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public static function getBanner($conn, $user_id) {
        $stmt = $conn->prepare("SELECT banner_path FROM company WHERE id_user = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public static function getCompanies($conn, $limit = 12, $offset = 0) {
        $stmt = $conn->prepare("SELECT company_name, banner_path, logo_path FROM company WHERE id_user != ? LIMIT ?, ?");
        $stmt->bind_param("iii", $_SESSION["user_id"], $offset, $limit);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public static function countCompanies($conn) {
        $stmt = $conn->prepare("SELECT COUNT(*) as total FROM company WHERE id_user != ?");
        $stmt->bind_param("i", $_SESSION["user_id"]);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc()["total"];
    }

    public static function getFilteredCompanies($conn, $limit, $offset, $filter) {
        $stmt = $conn->prepare("SELECT company.company_name, company.banner_path, company.logo_path FROM company 
            JOIN activity_sector ON company.id_sector = activity_sector.id_sector
            WHERE company.id_user != ? AND activity_sector.sector_name = ?
            LIMIT ?, ?");
        $stmt->bind_param("isii", $_SESSION["user_id"], $filter, $offset, $limit);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public static function countFilteredCompanies($conn, $filter) {
        $stmt = $conn->prepare("SELECT COUNT(*) as total FROM company 
            JOIN activity_sector ON company.id_sector = activity_sector.id_sector
            WHERE company.id_user != ? AND activity_sector.sector_name = ?");
        $stmt->bind_param("is", $_SESSION["user_id"], $filter);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc()["total"];
    }

    public static function getAllActivitySectors($conn) {
        $stmt = $conn->prepare("SELECT sector_name FROM activity_sector");
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}