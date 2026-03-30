<?php
namespace App\Controllers;

use App\Models\User;
use Core\Csrf;
use Core\Auth;
use Core\View;


class DelaccountController {

    public static function renderDeleteAccount() {
        if (session_status() === PHP_SESSION_NONE) session_start();

        if (!isset($_SESSION["user_id"])) {
            header("Location: /");
            exit();
        }

        $error = $_SESSION["flash_error"] ?? "";
        unset($_SESSION["flash_error"]);

        View::render("delete_account.twig", ["error" => $error, "csrf_token" => Csrf::generateToken(), 'role' => $_SESSION['role']]);
    }

    public static function deleteAccount() {
        if (session_status() === PHP_SESSION_NONE) session_start();

        $email = $_POST["email"];
        $password = $_POST["password"];

        $user = new User();
        $userData = $user->findByemail($email);

        if (!$userData || !password_verify($password, $userData["password"])) {
            View::render("delete_account.twig", [
                "error" => "Identifiant ou mot de passe incorrect.",
                "csrf_token" => Csrf::generateToken(),
                "role" => $_SESSION["role"]
            ]);
            return;
        } 
        elseif ($userData["id_user"] != $_SESSION["user_id"]) {
            View::render("delete_account.twig", [
                "error" => "Vous ne pouvez supprimer que votre propre compte.",
                "csrf_token" => Csrf::generateToken(),
                "role" => $_SESSION["role"]
            ]);
            return;
        } 
        else {
            $user->delete_account($_SESSION["user_id"]);
            session_destroy();
            header("Location: /");
            exit();
        }
    }
}