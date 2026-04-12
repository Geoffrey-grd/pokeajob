<?php
namespace App\Controllers;

use Core\Csrf;
use Core\Auth;
use Core\View;

use App\Controllers\GeneralController;

class LegalMentionsController {

    public static function renderLegalMentions() {

        if (session_status() === PHP_SESSION_NONE) session_start();

        if (!isset($_SESSION["user_id"])) {
            header("Location: /");
            exit();
        }

        $generalController = new GeneralController();
        $profile_pic_path = $generalController->checkprofilepic($_SESSION["user_id"], $_SESSION["role"]);

        View::render("legal_mentions.twig", ['role' => $_SESSION["role"], 'profile_pic_path' => $profile_pic_path]);

    }
}

?>