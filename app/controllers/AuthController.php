<?php
namespace App\Controllers;

use App\Models\User;
use App\Models\Sector;
use Core\Csrf;
use Core\Auth;
use Core\View;
require_once __DIR__ . "/../../config/database.php";


class AuthController {

    public function renderLoginRegister() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        global $conn;

        $sectors = Sector::getAllSectors($conn);

        $setup_mode = $_SESSION["flash_mode"] ?? "login";
        $setup_account_type = $_SESSION["flash_account_type"] ?? "etudiant";
        $error = $_SESSION["flash_error"] ?? "";

        unset($_SESSION["flash_mode"], $_SESSION["flash_account_type"], $_SESSION["flash_error"]);

        View::render("login-register.twig", ["setup_mode" => $setup_mode, "setup_account_type" => $setup_account_type, "error" => $error, "csrf_token" => Csrf::generateToken(), "sectors" => $sectors]);
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
        global $conn;

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            Csrf::verifyToken($_POST["csrf_token"]);
            $email = $_POST["email"];
            $password = $_POST["password"];

            $user = User::findByemail($conn, $email);

            if ($user && password_verify($password, $user["password"])) {
                $_SESSION["user_id"] = $user["id_user"];
                $_SESSION["email"] = $user["email"];
                $_SESSION["role"] = $user["role"];

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
        global $conn;

        Csrf::verifyToken($_POST["csrf_token"]);


        $email = $_POST["email"];
        $password = $_POST["password"];
        $existingUser = User::findByemail($conn, $email);

        if ($existingUser !== null) {
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
            User::create_company($conn, $email, $password, $company_name, $address, $sectors, $phone, $ciret, $role);
        } 
        else if ($account_type == "pilote") {
            $name= $_POST["name"];
            $last_name = $_POST["last_name"];
            $phone = $_POST["phone"];
            $school = $_POST["school"];
            $role = "pilote";
            User::create_pilot($conn, $email, $password, $name, $last_name, $phone, $school, $role);
        } 
        else {
            $name= $_POST["name"];
            $last_name = $_POST["last_name"];
            $school = $_POST["school"];
            $role = "etudiant";
            $pilot= $_POST["training_pilot"];
            User::create_student($conn, $email, $password, $name, $last_name, $school, $pilot, $role);
        }
        $SESSION["flash_mode"] = "login";
        header("Location: /");
        exit();
    }

    public static function logout() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        session_destroy();
        header("Location: /");
        exit();
    }


    public static function deleteAccount() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        global $conn;

        if (!isset($_SESSION["user_id"])) {
            header("Location: /");
            exit();
        }

        $error = "";

        if ($_SERVER["REQUEST_METHOD"] == "POST") {

            Csrf::verifyToken($_POST["csrf_token"]);

            $email = $_POST["email"];
            $password = $_POST["password"];

            $user = User::findByemail($conn, $email);

            if (!$user || !password_verify($password, $user["password"])) {

                $error = "Identifiant ou mot de passe incorrect.";

            } elseif ($user["id_user"] != $_SESSION["user_id"]) {

                $error = "Erreur de sécurité.";

            } else {

                User::delete_account($conn, $_SESSION["user_id"]);

                session_destroy();

                header("Location: /");
                exit();
            }
        }
        View::render("delete_account.twig", ["error" => $error, "csrf_token" => Csrf::generateToken(), 'role' => $_SESSION['role']]);
    }
}