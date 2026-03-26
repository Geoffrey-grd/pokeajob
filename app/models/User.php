<?php
 
namespace App\Models;
 
require_once __DIR__ . "/../../config/database.php";
 
class User {
 
    public static function findByemail($conn, $email) {
        $stmt = $conn->prepare("SELECT * FROM user WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
 
    public static function delete_account($conn, $user_id) {
        $stmt = $conn->prepare("DELETE FROM user WHERE id_user = ?");
        $stmt->bind_param("i", $user_id);
        return $stmt->execute();
    }
}