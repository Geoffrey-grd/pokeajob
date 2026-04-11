<?php

namespace App\Controllers;

use Core\Csrf;
use Core\Auth;
use Core\View;
use App\Models\Offer;

use App\Controllers\GeneralController;

class WishListController {

    public function renderWishList() {
        if (session_status() === PHP_SESSION_NONE) session_start();

        if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "etudiant") {
            header("Location: /");
            exit();
        }

        $offerModel = new Offer();
        $cards = $offerModel->getWishListByUserId($_SESSION["user_id"]);

        $generalController = new GeneralController();
        $profile_pic_path = $generalController->checkprofilepic($_SESSION["user_id"], $_SESSION["role"]);
        
        View::render("wishlist.twig", ["role" => $_SESSION["role"], "cards" => $cards, 'profile_pic_path' => $profile_pic_path]);
    }

}