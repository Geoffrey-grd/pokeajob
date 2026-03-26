<?php

namespace App\Models;

require_once __DIR__ . "/../../config/database.php";

class Sector {
    
    public static function getAllSectors($conn) {
        $stmt = $conn->prepare("SELECT * FROM activity_sector");
        $stmt->execute();

        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    }

    public static function getidsector($conn, $sector) {
        $stmt = $conn->prepare("SELECT id_sector FROM activity_sector WHERE sector_name = ?");
        $stmt->bind_param("s", $sector);
        $stmt->execute();

        return $stmt->get_result();
    }
}