<?php

namespace App\Models;

require_once __DIR__ . "/../../config/database.php";

class Pilot extends User {

    public static function getAllPilots($conn) {
        $stmt = $conn->prepare("SELECT pilot.id_user, pilot.name, pilot.last_name FROM pilot");
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public static function create_pilot($conn, $email, $password, $name, $last_name, $phone, $school, $role) {
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("INSERT INTO user (email, password, role) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $email, $passwordHash, $role);
        $stmt->execute();

        $id_user = $conn->insert_id;

        $stmt = $conn->prepare("INSERT INTO pilot (id_user, name, last_name, phone, school) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("issss", $id_user, $name, $last_name, $phone, $school);
        return $stmt->execute();
    }

    public static function getPilotInformations($conn, $id_user) {
        $stmt = $conn->prepare("SELECT user.email, pilot.name, pilot.last_name, pilot.phone, pilot.school, pilot.profile_pic_path
            FROM pilot JOIN user ON pilot.id_user = user.id_user WHERE pilot.id_user = ?");
        $stmt->bind_param("i", $id_user);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public static function updatePilot($conn, $id_user, $profile_pic_path, $last_name, $name, $email, $phone, $school) {
        $stmt = $conn->prepare("UPDATE user JOIN pilot ON user.id_user = pilot.id_user SET user.email = ?, pilot.name = ?, pilot.last_name = ?, pilot.phone = ?, pilot.school = ?, pilot.profile_pic_path = ? WHERE user.id_user = ?");
        $stmt->bind_param("ssssssi", $email, $name, $last_name, $phone, $school, $profile_pic_path, $id_user);
        return $stmt->execute();
    }

    public static function getPilotProfilePicture($conn, $user_id) {
        $stmt = $conn->prepare("SELECT profile_pic_path FROM pilot WHERE id_user = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
}