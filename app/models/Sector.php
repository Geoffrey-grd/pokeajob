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

    // public static function getAllSectors($conn) {
    // $stmt = $conn->prepare("SELECT sector_name FROM sector");
    // $stmt->execute();

    // $allsectors = [];
    // $results = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    // foreach ($results as $row) {
    //     $sectorString = $row['sectors'];
    //     $sectors = explode(":", $sectorString);
    //     foreach ($sectors as $t) {
    //         if (!empty($t) && !in_array($t, $allsectors)) {
    //             $allsectors[] = $t;
    //         }
    //     }
    // }
    // return $allsectors;
    //}
}
