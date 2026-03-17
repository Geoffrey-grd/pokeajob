<?php
namespace App\Models;

class User {

    public static function findByemail($conn, $email) {

        $stmt = $conn->prepare("SELECT * FROM user WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();

        return $stmt->get_result()->fetch_assoc();
    }


    public static function create_student($conn, $email, $password, $name, $last_name, $school, $pilot, $role) {

        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("INSERT INTO user (email, password, role) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $email, $passwordHash, $role);
        $stmt->execute();

        $id_user = $conn->insert_id;
        $id_pilot = null;

        $stmt = $conn->prepare("INSERT INTO student (id_user, name, last_name, school, id_pilot) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("isssi", $id_user, $name, $last_name, $school, $id_pilot);

        return $stmt->execute();
    }

    public static function create_company($conn, $email, $password, $company_name, $address, $phone, $ciret, $role) {

        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("INSERT INTO user (email, password, role) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $email, $passwordHash, $role);
        $stmt->execute();

        $id_user = $conn->insert_id;

        $stmt = $conn->prepare("INSERT INTO company (id_user, company_name, address, phone, ciret) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("issss", $id_user, $company_name, $address, $phone, $ciret);

        return $stmt->execute();
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


    public static function delete_account($conn, $user_id) {

        $stmt = $conn->prepare("DELETE FROM user WHERE id_user = ?");
        $stmt->bind_param("i", $user_id);

        return $stmt->execute();
    }
}