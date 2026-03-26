<?php

namespace App\Models;

require_once __DIR__ . "/../../config/database.php";

class Student extends User {

    public static function create_student($conn, $email, $password, $name, $last_name, $school, $pilot, $role) {
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("INSERT INTO user (email, password, role) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $email, $passwordHash, $role);
        $stmt->execute();

        $id_user = $conn->insert_id;

        $stmt = $conn->prepare("INSERT INTO student (id_user, name, last_name, school, id_pilot) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("isssi", $id_user, $name, $last_name, $school, $pilot);
        return $stmt->execute();
    }

    public static function getStudentInformations($conn, $id_user) {
        $stmt = $conn->prepare("SELECT user.email, student.name, student.last_name, student.school, student.description, student.profile_pic_path, pilot.name AS pilot_name, pilot.last_name AS pilot_last_name
            FROM student JOIN user ON student.id_user = user.id_user JOIN pilot ON student.id_pilot = pilot.id_user WHERE student.id_user = ?");
        $stmt->bind_param("i", $id_user);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public static function updateStudent($conn, $id_user, $profile_pic_path, $last_name, $name, $email, $school, $description) {
        $stmt = $conn->prepare("UPDATE user JOIN student ON user.id_user = student.id_user SET user.email = ?, student.name = ?, student.last_name = ?, student.school = ?, student.description = ?, student.profile_pic_path = ? WHERE user.id_user = ?");
        $stmt->bind_param("ssssssi", $email, $name, $last_name, $school, $description, $profile_pic_path, $id_user);
        return $stmt->execute();
    }

    public static function getStudentProfilePicture($conn, $user_id) {
        $stmt = $conn->prepare("SELECT profile_pic_path FROM student WHERE id_user = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
}