<?php
namespace App\Controllers;

use App\Models\User;
use App\Models\Student;
use App\Models\Company;
use App\Models\Pilot;
use App\Models\Sector;
use Core\Csrf;
use Core\Auth;
use Core\View;


class AuthController {

    public function renderLoginRegister() {
        if (session_status() === PHP_SESSION_NONE) session_start();

        $sector = new Sector();
        $pilot = new Pilot();

        $sectors = $sector->getAllSectors();
        $pilots = $pilot->getAllPilots();

        $setup_mode = $_SESSION["flash_mode"] ?? "login";
        $setup_account_type = $_SESSION["flash_account_type"] ?? "etudiant";
        $error = $_SESSION["flash_error"] ?? "";

        unset($_SESSION["flash_mode"], $_SESSION["flash_account_type"], $_SESSION["flash_error"]);

        View::render("login-register.twig", ["setup_mode" => $setup_mode, "setup_account_type" => $setup_account_type, "error" => $error, "csrf_token" => Csrf::generateToken(), "sectors" => $sectors, "pilots" => $pilots]);
    }

    public function form_type() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if ($_POST["auth_mode"] == "login") {
            $this->login();
        } 
        else {
            $account_type = $_POST["account_type"];
            $this->register($account_type);
        }
    }

    public function login() {
        if (session_status() === PHP_SESSION_NONE) session_start();

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            Csrf::verifyToken($_POST["csrf_token"]);
            $email = $_POST["email"];
            $password = $_POST["password"];

            $user = new User();
            $userData = $user->findByemail($email);

            if ($userData && password_verify($password, $userData["password"])) {
                $_SESSION["user_id"] = $userData["id_user"];
                $_SESSION["email"] = $userData["email"];
                $_SESSION["role"] = $userData["role"];

                header("Location: /search_page");
                exit(); 
            } 
            else {
                $_SESSION["flash_error"] = "Identifiant ou mot de passe incorrect.";
                $_SESSION["flash_mode"] = "login";
                header("Location: /");
                exit();
            }
        }
    }

    public function register($account_type) {
        if (session_status() === PHP_SESSION_NONE) session_start();

        Csrf::verifyToken($_POST["csrf_token"]);

        $email = $_POST["email"];
        $password = $_POST["password"];

        $user = new User();
        $existingUser = $user->findByemail($email);

        if ($existingUser !== false) {
            $_SESSION["flash_error"] = "This email is already in use.";
            $_SESSION["flash_mode"] = "signup";
            $_SESSION["flash_account_type"] = $account_type;
            header("Location: /");
            exit();
        }
         
        if ($account_type == "entreprise") {
            $company_name = $_POST["company_name"];
            $address = $_POST["company_address"];
            $sectors = $_POST["activity_sector"];
            $phone = $_POST["phone"];
            $ciret = $_POST["company_ciret"];
            $role = "entreprise";
            $company = new Company();
            $company->create_company($email, $password, $company_name, $address, $sectors, $phone, $ciret, $role);
        } 
        else if ($account_type == "pilote") {
            $name = $_POST["name"];
            $last_name = $_POST["last_name"];
            $phone = $_POST["phone"];
            $school = $_POST["school"];
            $role = "pilote";
            $pilot = new Pilot();
            $pilot->create_pilot($email, $password, $name, $last_name, $phone, $school, $role);
        } 
        else {
            $name = $_POST["name"];
            $last_name = $_POST["last_name"];
            $school = $_POST["school"];
            $role = "etudiant";
            $pilot = $_POST["training_pilot"];
            $student = new Student();
            $student->create_student($email, $password, $name, $last_name, $pilot, $role);
        }
        $_SESSION["flash_mode"] = "login";
        header("Location: /");
        exit();
    }

    public static function logout() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        session_destroy();
        header("Location: /");
        exit();
    }
}