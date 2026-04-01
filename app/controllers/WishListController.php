<?php

namespace App\Controllers;

use Core\Csrf;
use Core\Auth;
use Core\View;
use App\Models\Offer;


class WishListController {

    public function renderWishList() {
        if (session_status() === PHP_SESSION_NONE) session_start();

        if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "etudiant") {
            header("Location: /");
            exit();
        }

        $offerModel = new Offer();
        $cards = $offerModel->getWishListByUserId($_SESSION["user_id"]);

        View::render("wishlist.twig", ["role" => $_SESSION["role"], "cards" => $cards]);
    }

}