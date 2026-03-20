<?php

namespace App\Models;

require_once __DIR__ . "/../../config/database.php";

class Companies {

    public static function getCompanies($conn) {
        $stmt = $conn->prepare("SELECT company_name FROM company WHERE id_user != ?");
        $stmt->bind_param("i", $_SESSION["user_id"]);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public static function countCompanies($conn) {
        $stmt = $conn->prepare("SELECT COUNT(*) as total FROM company WHERE id_user != ?");
        $stmt->bind_param("i", $_SESSION["user_id"]);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc()["total"];
    }
}