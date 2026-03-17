<?php
namespace App\Controllers;

use App\Models\User;
use Core\Csrf;
use Core\Auth;
use Core\View;
require_once __DIR__ . "/../../config/database.php";


class AuthController {

    public function renderLoginRegister() {
        View::render("login-register.twig", ["csrf_token" => Csrf::generateToken()]);
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

        $error = "";

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            Csrf::verifyToken($_POST["csrf_token"]);
            $email = $_POST["email"];
            $password = $_POST["password"];

            $user = User::findByemail($conn, $email);

            if ($user && password_verify($password, $user["password"])) {
                $_SESSION["user_id"] = $user["id_user"];
                $_SESSION["email"] = $user["email"];
                $_SESSION["role"] = $user["role"];

                header("Location: /offers-list");
                exit(); 
            } else {
                $error = "Identifiant ou mot de passe incorrect.";
            }
        }
        View::render("login-register.twig", ["csrf_token" => Csrf::generateToken(), "error" => $error]);
    }


    public function register($account_type) {
        if (session_status() === PHP_SESSION_NONE) session_start();
        global $conn;

        $error = "";

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            Csrf::verifyToken($_POST["csrf_token"]);


            $email = $_POST["email"];
            $password = $_POST["password"];

            $existingUser = User::findByemail($conn, $email);

            if ($existingUser !== null) {
                $error = "Cet email est déjà utilisé.";
            } 
            else {
                if ($account_type == "entreprise") {
                    $company_name = $_POST["company_name"];
                    $address = $_POST["company_address"];
                    $phone = $_POST["phone"];
                    $ciret = $_POST["company_ciret"];
                    $role = "entreprise";
                    User::create_company($conn, $email, $password, $company_name, $address, $phone, $ciret, $role);
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
            }
        }

        View::render("login-register.twig", ["csrf_token" => Csrf::generateToken(), "error" => $error]);
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
        View::render("delete_account.twig", ["error" => $error, "csrf_token" => Csrf::generateToken()]);
    }
}
