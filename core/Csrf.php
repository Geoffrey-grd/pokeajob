<?php
namespace Core;

class Csrf {

    public static function generateToken() {

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (empty($_SESSION["csrf_token"])) {
            $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
        }

        return $_SESSION["csrf_token"];
    }

    public static function verifyToken($token) {

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION["csrf_token"]) || $token !== $_SESSION["csrf_token"]) {
            die("Requête invalide (CSRF)");
        }

    }

}
