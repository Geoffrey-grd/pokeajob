<?php

namespace App\Models;

use PDO;

class Student extends User {
    public function __construct() {
        parent::__construct();
    }

    public function create_student($email, $password, $name, $last_name, $school, $pilot, $role) {
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $this->conn->prepare("INSERT INTO user (email, password, role) VALUES (?, ?, ?)");
        $stmt->execute([$email, $passwordHash, $role]);

        $id_user = $this->conn->insert_id;

        $stmt = $this->conn->prepare("INSERT INTO student (id_user, name, last_name, school, id_pilot) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$id_user, $name, $last_name, $school, $pilot]);
        return $stmt->execute();
    }

    public function getStudentInformations($id_user) {
        $stmt = $this->conn->prepare("SELECT user.email, student.name, student.last_name, student.school, student.description, student.profile_pic_path, pilot.name AS pilot_name, pilot.last_name AS pilot_last_name
            FROM student JOIN user ON student.id_user = user.id_user JOIN pilot ON student.id_pilot = pilot.id_user WHERE student.id_user = ?");
        $stmt->execute([$id_user]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateStudent($id_user, $profile_pic_path, $last_name, $name, $email, $school, $description) {
        $stmt = $this->conn->prepare("UPDATE user JOIN student ON user.id_user = student.id_user SET user.email = ?, student.name = ?, student.last_name = ?, student.school = ?, student.description = ?, student.profile_pic_path = ? WHERE user.id_user = ?");
        $stmt->execute([$email, $name, $last_name, $school, $description, $profile_pic_path, $id_user]);
        return $stmt->execute();
    }

    public function getStudentProfilePicture($user_id) {
        $stmt = $this->conn->prepare("SELECT profile_pic_path FROM student WHERE id_user = ?");
        $stmt->execute([$user_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}