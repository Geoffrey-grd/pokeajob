<?php

namespace App\Models;

require_once __DIR__ . "/../../config/database.php";

class Offer {
    
    public static function getAllOffers($conn, $limit = 9, $offset = 0) {
        
            $stmt = $conn->prepare("SELECT offer.*, company.company_name, company.banner_path, company.logo_path
                FROM offer JOIN company ON offer.id_company = company.id_user
                WHERE offer.id_company != ?
                ORDER BY offer.release_date ASC
                LIMIT ?, ?");
            $stmt->bind_param("iii", $_SESSION["user_id"], $offset, $limit);
            $stmt->execute();
            $result = $stmt->get_result();
            
            return $result->fetch_all(MYSQLI_ASSOC);
    }

    public static function countOffers($conn) {
        $stmt = $conn->prepare("SELECT COUNT(*) as total FROM offer WHERE id_company != ?");
        $stmt->bind_param("i", $_SESSION["user_id"]);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc()["total"];
    }
}
