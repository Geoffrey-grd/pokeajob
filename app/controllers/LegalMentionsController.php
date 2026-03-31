<?php
namespace App\Controllers;

use Core\Csrf;
use Core\Auth;
use Core\View;

class LegalMentionsController {

    public static function renderLegalMentions() {

        if (session_status() === PHP_SESSION_NONE) session_start();

        if (!isset($_SESSION["user_id"])) {
            header("Location: /");
            exit();
        }

        View::render("legal_mentions.twig");

    }
}

?>