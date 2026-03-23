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

    public static function create_company($conn, $email, $password, $company_name, $address, $sector, $phone, $ciret, $role) {

        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("INSERT INTO user (email, password, role) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $email, $passwordHash, $role);
        $stmt->execute();

        $id_user = $conn->insert_id;

        $stmt = $conn->prepare("INSERT INTO company (id_user, company_name, address, id_sector, phone, ciret) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ississ", $id_user, $company_name, $address, $sector, $phone, $ciret);

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

    public static function getStudentById($conn, $id_user) {
        $stmt = $conn->prepare("SELECT user.email, student.name, student.last_name, student.school, student.description, student.profile_pic_path, pilot.name AS pilot_name, pilot.last_name AS pilot_last_name
            FROM student JOIN user ON student.id_user = user.id_user JOIN pilot ON student.id_pilot = pilot.id_user WHERE student.id_user = ?");
        $stmt->bind_param("i", $id_user);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public static function getPilotById($conn, $id_user) {
        $stmt = $conn->prepare("SELECT user.email, pilot.name, pilot.last_name, pilot.phone, pilot.school, pilot.profile_pic_path
            FROM pilot JOIN user ON pilot.id_user = user.id_user WHERE pilot.id_user = ?");
        $stmt->bind_param("i", $id_user);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public static function getCompanyById($conn, $id_user) {
        $stmt = $conn->prepare("SELECT user.email, company.company_name, company.address, activity_sector.sector_name, company.phone, company.ciret, company.description, company.banner_path, company.logo_path, company.rating
            FROM company JOIN user ON company.id_user = user.id_user JOIN activity_sector ON company.id_sector = activity_sector.id_sector WHERE company.id_user = ?");
        $stmt->bind_param("i", $id_user);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public static function delete_account($conn, $user_id) {

        $stmt = $conn->prepare("DELETE FROM user WHERE id_user = ?");
        $stmt->bind_param("i", $user_id);

        return $stmt->execute();
    }


}