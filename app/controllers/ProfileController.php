<?php
namespace App\Controllers;

use App\Models\User;
use App\Models\Student;
use App\Models\Company;
use App\Models\Pilot;
use Core\Csrf;
use Core\Auth;
use Core\View;


class ProfileController {

    public function renderProfile($editmode = false) {
        if (session_status() === PHP_SESSION_NONE) session_start();

        if (!isset($_SESSION["user_id"])){
            header("Location: /");
            exit();
        }

        $error = "";

        if (isset($_SESSION["flash_error"])) { $error = $_SESSION["flash_error"]; }
        if (isset($_SESSION["flash_mode"])) { $editmode = $_SESSION["flash_mode"]; }
        unset($_SESSION["flash_error"], $_SESSION["flash_mode"]);

        if ($_SESSION["role"] == "etudiant") {
            $student = new Student();
            $userData = $student->getStudentInformations($_SESSION["user_id"]);
        } 
        else if ($_SESSION["role"] == "entreprise") {
            $company = new Company();
            $userData = $company->getCompanyInformations($_SESSION["user_id"]);
        }
        else if ($_SESSION["role"] == "pilote") {
            $pilot = new Pilot();
            $userData = $pilot->getPilotInformations($_SESSION["user_id"]);
        }

        View::render("profile.twig", ["role" => $_SESSION["role"], "userData" => $userData, "editmode" => $editmode, "csrf_token" => Csrf::generateToken(), "error" => $error]);
    }

    public function editProfile() {
        $this->renderProfile(true);
    }

    public function modifyProfile() {
        if (session_status() === PHP_SESSION_NONE) session_start();

        Csrf::verifyToken($_POST["csrf_token"]);

        if ($_SESSION["role"] == "etudiant") {
            $student = new Student();
            if ($_FILES["profile_pic"]["error"] !== UPLOAD_ERR_NO_FILE) {
                $this->checkpicture($_FILES["profile_pic"], "1:1");
                $profile_pic_path = $this->moveFile($_FILES["profile_pic"], "profile_img");
            }
            else {
                $profile_pic_path = $student->getStudentProfilePicture($_SESSION["user_id"])["profile_pic_path"];
            }
            $student->updateStudent($_SESSION["user_id"], $profile_pic_path, $_POST["last_name"], $_POST["name"], $_POST["email"], $_POST["description"]);
        } 
        else if ($_SESSION["role"] == "entreprise") {
            $company = new Company();
            if ($_FILES["logo"]["error"] !== UPLOAD_ERR_NO_FILE) {
                $this->checkpicture($_FILES["logo"], "1:1");
                $logo_path = $this->moveFile($_FILES["logo"], "company_logo");
            }
            else {
                $logo_path = $company->getLogo($_SESSION["user_id"])["logo_path"];
            }
            if ($_FILES["banner"]["error"] !== UPLOAD_ERR_NO_FILE) {
                $this->checkpicture($_FILES["banner"], "5:1");
                $banner_path = $this->moveFile($_FILES["banner"], "company_banner");
            }
            else {
                $banner_path = $company->getBanner($_SESSION["user_id"])["banner_path"];
            }
            $company->updateCompany($_SESSION["user_id"], $logo_path, $banner_path, $_POST["company_name"], $_POST["email"], $_POST["phone"], $_POST["description"]);
        }
        else if ($_SESSION["role"] == "pilote") {
            $pilot = new Pilot();
            if ($_FILES["profile_pic"]["error"] !== UPLOAD_ERR_NO_FILE) {
                $this->checkpicture($_FILES["profile_pic"], "1:1");
                $profile_pic_path = $this->moveFile($_FILES["profile_pic"], "profile_img");
            }
            else {
                $profile_pic_path = $pilot->getPilotProfilePicture($_SESSION["user_id"])["profile_pic_path"];
            }
            $pilot->updatePilot($_SESSION["user_id"], $profile_pic_path, $_POST["last_name"], $_POST["name"], $_POST["email"], $_POST["phone"], $_POST["school"]);
        }

        header("Location: /profile"); 
    }

    public function checkpicture($file, $type) {
        if ($file["error"] === UPLOAD_ERR_OK) {
            $error = "";
            $tmp = getimagesize($file["tmp_name"]);
            $width = $tmp[0];
            $height = $tmp[1];
            $ratio = $width / $height;
            if ($type == "1:1" && $ratio != 1) {
                if ($_SESSION["role"] == "etudiant" || $_SESSION["role"] == "pilote") {
                    $error = "Profile picture must be in 1:1 ratio";
                }
                else if ($_SESSION["role"] == "entreprise") {
                    $error = "Logo must be in 1:1 ratio";  
                }
                $_SESSION["flash_error"] = $error;
                $_SESSION["flash_mode"] = true;
                header("Location: /profile");
                exit();
            }
            else if ($type == "5:1" && $ratio != 5) {
                $error = "Banner must be in 5:1 ratio";
                $_SESSION["flash_error"] = $error;
                $_SESSION["flash_mode"] = true;
                header("Location: /profile");
                exit();
            }
        }
        else {
            $error = "Error uploading file";
            $_SESSION["flash_error"] = $error;
            $_SESSION["flash_mode"] = true;
            header("Location: /profile");
            exit();
        }
    }

    public function moveFile($file, $type) {
        $base_dir = realpath(__DIR__ . "/../../public/images/uploads/" . $type) . "/";
        $extension = pathinfo($file["name"], PATHINFO_EXTENSION);
        $target_file = $base_dir . $_SESSION["user_id"] . "." . $extension;
        foreach (glob($base_dir . $_SESSION["user_id"] . ".*") as $existing_file) {
            unlink($existing_file);
        }
        move_uploaded_file($file["tmp_name"], $target_file);
        $bdd_path = str_replace("/var/www/pokeajob/public", "", $target_file);
        return $bdd_path;
    }
}