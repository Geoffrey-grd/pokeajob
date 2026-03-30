<?php
namespace App\Controllers;

use Core\Csrf;
use Core\Auth;
use Core\View;
use App\Models\Offer;

class CreateOfferController {

    public function renderCreateOffer() {
        if (session_status() === PHP_SESSION_NONE) session_start();

        if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "entreprise") {
            header("Location: /");
            exit();
        }

        $offerModel = new Offer();
        $domains = $offerModel->getAllDomains();

        View::render("create_offer.twig", ["role" => $_SESSION["role"], "csrf_token" => Csrf::generateToken(), "domains" => $domains]);
    }


    public function createOffer() {
        if (session_status() === PHP_SESSION_NONE) session_start();

        Csrf::verifyToken($_POST["csrf_token"]);

        $offerModel = new Offer();
        $offerModel->create_offer($_SESSION["user_id"], $_POST["domain"], $_POST["offer_object"], $_POST["place"], $_POST["annual_salary"], $_POST["description"]);

        header("Location: /create_offer");
        exit();
    }
 
}