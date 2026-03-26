<?php
namespace App\Models;

class User {

    public static function findByemail($conn, $email) {

        $stmt = $conn->prepare("SELECT * FROM user WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();

        return $stmt->get_result()->fetch_assoc();
    }

    public static function getAllPilots($conn) {
        $stmt = $conn->prepare("SELECT pilot.id_user, pilot.name, pilot.last_name FROM pilot");
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
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

    public static function getStudentInformations($conn, $id_user) {
        $stmt = $conn->prepare("SELECT user.email, student.name, student.last_name, student.school, student.description, student.profile_pic_path, pilot.name AS pilot_name, pilot.last_name AS pilot_last_name
            FROM student JOIN user ON student.id_user = user.id_user JOIN pilot ON student.id_pilot = pilot.id_user WHERE student.id_user = ?");
        $stmt->bind_param("i", $id_user);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public static function getPilotInformations($conn, $id_user) {
        $stmt = $conn->prepare("SELECT user.email, pilot.name, pilot.last_name, pilot.phone, pilot.school, pilot.profile_pic_path
            FROM pilot JOIN user ON pilot.id_user = user.id_user WHERE pilot.id_user = ?");
        $stmt->bind_param("i", $id_user);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public static function getCompanyInformations($conn, $id_user) {
        $stmt = $conn->prepare("SELECT user.email, company.company_name, company.address, activity_sector.sector_name, company.phone, company.ciret, company.description, company.banner_path, company.logo_path, company.rating
            FROM company JOIN user ON company.id_user = user.id_user JOIN activity_sector ON company.id_sector = activity_sector.id_sector WHERE company.id_user = ?");
        $stmt->bind_param("i", $id_user);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public static function updateStudent($conn, $id_user, $profile_pic_path, $last_name, $name, $email, $school, $description) {

        $stmt = $conn->prepare("UPDATE user JOIN student ON user.id_user = student.id_user SET user.email = ?, student.name = ?, student.last_name = ?, student.school = ?, student.description = ?, student.profile_pic_path = ? WHERE user.id_user = ?");
        $stmt->bind_param("ssssssi", $email, $name, $last_name, $school, $description, $profile_pic_path, $id_user);

        return $stmt->execute();
    }

    public static function updatePilot($conn, $id_user, $profile_pic_path, $last_name, $name, $email, $phone, $school) {

        $stmt = $conn->prepare("UPDATE user JOIN pilot ON user.id_user = pilot.id_user SET user.email = ?, pilot.name = ?, pilot.last_name = ?, pilot.phone = ?, pilot.school = ?, pilot.profile_pic_path = ? WHERE user.id_user = ?");
        $stmt->bind_param("ssssssi", $email, $name, $last_name, $phone, $school, $profile_pic_path, $id_user);

        return $stmt->execute();
    }

    public static function updateCompany($conn, $id_user, $logo_path, $banner_path, $company_name, $email, $phone, $description) {

        $stmt = $conn->prepare("UPDATE user JOIN company ON user.id_user = company.id_user SET user.email = ?, company.company_name = ?, company.phone = ?, company.description = ?, company.logo_path = ?, company.banner_path = ? WHERE user.id_user = ?");
        $stmt->bind_param("ssssssi", $email, $company_name, $phone, $description, $logo_path, $banner_path, $id_user);

        return $stmt->execute();
    }

    public static function delete_account($conn, $user_id) {

        $stmt = $conn->prepare("DELETE FROM user WHERE id_user = ?");
        $stmt->bind_param("i", $user_id);

        return $stmt->execute();
    }

    public static function getStudentProfilePicture($conn, $user_id) {
        $stmt = $conn->prepare("SELECT profile_pic_path FROM student WHERE id_user = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public static function getPilotProfilePicture($conn, $user_id) {
        $stmt = $conn->prepare("SELECT profile_pic_path FROM pilot WHERE id_user = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
    
    public static function getLogo($conn, $user_id) {
        $stmt = $conn->prepare("SELECT logo_path FROM company WHERE id_user = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public static function getBanner($conn, $user_id) {
        $stmt = $conn->prepare("SELECT banner_path FROM company WHERE id_user = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }


}