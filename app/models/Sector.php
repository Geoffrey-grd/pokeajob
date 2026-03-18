<?php

namespace App\Models;

require_once __DIR__ . "/../../config/database.php";

class Sector {
    
    public static function getAllSectors($conn) {
    $stmt = $conn->prepare("SELECT sectors FROM company");
    $stmt->execute();

    $allsectors = [];
    $results = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    foreach ($results as $row) {
        $sectorString = $row['sectors'];
        $sectors = explode(":", $sectorString);
        foreach ($sectors as $t) {
            if (!empty($t) && !in_array($t, $allsectors)) {
                $allsectors[] = $t;
            }
        }
    }
    return $allsectors;
}
}
