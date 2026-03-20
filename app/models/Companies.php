<?php

namespace App\Models;

require_once __DIR__ . "/../../config/database.php";

class Companies {

    public static function getCompanies($conn, $limit = 9, $offset = 0) {
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