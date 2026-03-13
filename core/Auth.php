<?php
namespace Core;

class Auth {

    public static function check() {

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION["user_id"])) {
            header("Location: login.php");
            exit();
        }

    }

    public static function requireRole($role) {

        self::check();

        if ($_SESSION["role"] !== $role) {
            echo "Accès refusé";
            exit();
        }
        else {
            return true;
        }

}

}
