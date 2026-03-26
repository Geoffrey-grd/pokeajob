<?php
namespace App\Controllers;

use App\Models\User;
use Core\Csrf;
use Core\Auth;
use Core\View;
require_once __DIR__ . "/../../config/database.php";


class DelaccountController {

    public static function renderDeleteAccount() {
        if (session_status() === PHP_SESSION_NONE) session_start();

        if (!isset($_SESSION["user_id"])) {
            header("Location: /");
            exit();
        }

        View::render("delete_account.twig", ["error" => $error, "csrf_token" => Csrf::generateToken(), 'role' => $_SESSION['role']]);
    }




    public static function deleteAccount() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        global $conn;

        $error = "";

        //Csrf::verifyToken($_POST["csrf_token"]);

        $email = $_POST["email"];
        $password = $_POST["password"];

        $user = User::findByemail($conn, $email);

        if (!$user || !password_verify($password, $user["password"])) {
            $_SESSION["flash_error"] = "Identifiant ou mot de passe incorrect.";
            header("Location: /delete_account_page");

        } 
        elseif ($user["id_user"] != $_SESSION["user_id"]) {
            $_SESSION["flash_error"] = "Vous ne pouvez supprimer que votre propre compte.";
            header("Location: /delete_account_page");
        } 
        else {
            User::delete_account($conn, $_SESSION["user_id"]);
            session_destroy();
            header("Location: /");
            exit();
        }
    }
}