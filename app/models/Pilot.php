<?php

namespace App\Models;

use PDO;

class Pilot extends User {

    public function __construct() {
        parent::__construct();
    }

    public function getAllPilots() {
        $stmt = $this->conn->prepare("SELECT pilot.id_user, pilot.name, pilot.last_name FROM pilot");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create_pilot($email, $password, $name, $last_name, $phone, $school, $role) {
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $this->conn->prepare("INSERT INTO user (email, password, role) VALUES (?, ?, ?)");
        $stmt->execute([$email, $passwordHash, $role]);

        $id_user = $this->conn->lastInsertId();

        $stmt = $this->conn->prepare("INSERT INTO pilot (id_user, name, last_name, phone, school) VALUES (?, ?, ?, ?, ?)");
        return $stmt->execute([$id_user, $name, $last_name, $phone, $school]);
    }

    public function getPilotInformations($id_user) {
        $stmt = $this->conn->prepare("SELECT user.email, pilot.name, pilot.last_name, pilot.phone, pilot.school, pilot.profile_pic_path
            FROM pilot JOIN user ON pilot.id_user = user.id_user WHERE pilot.id_user = ?");
        $stmt->execute([$id_user]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updatePilot($id_user, $profile_pic_path, $last_name, $name, $email, $phone, $school) {
        $stmt = $this->conn->prepare("UPDATE user JOIN pilot ON user.id_user = pilot.id_user SET user.email = ?, pilot.name = ?, pilot.last_name = ?, pilot.phone = ?, pilot.school = ?, pilot.profile_pic_path = ? WHERE user.id_user = ?");
        return $stmt->execute([$email, $name, $last_name, $phone, $school, $profile_pic_path, $id_user]);
    }

    public function getPilotProfilePicture($user_id) {
        $stmt = $this->conn->prepare("SELECT profile_pic_path FROM pilot WHERE id_user = ?");
        $stmt->execute([$user_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}