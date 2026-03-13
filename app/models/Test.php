<?php

namespace App\Models;

require_once __DIR__ . "/../../config/database.php";

class Test {
    public function getAll($conn) {

        $sql = "SELECT * FROM user";

        return $conn->query($sql);
    }
}
