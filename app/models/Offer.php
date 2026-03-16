<?php

namespace App\Models;

require_once __DIR__ . "/../../config/database.php";

class Offer {
    
    public static function getAllOffers($conn) {
        
            $stmt = $conn->prepare("SELECT offer.*, company.banner_path, company.logo_path
                FROM offer JOIN company ON offer.id_company = company.id_user
                WHERE offer.id_company != ?
                ORDER BY offer.start_date ASC");
            $stmt->bind_param("i", $_SESSION["user_id"]);
            $stmt->execute();

            return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}
