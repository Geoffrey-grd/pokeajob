<?php
namespace App\Controllers;

use Core\Csrf;
use Core\Auth;
use Core\View;
use App\Models\Offer;

use App\Controllers\GeneralController;

class CreateOfferController {

    public function renderCreateOffer($editmode = false) {
        if (session_status() === PHP_SESSION_NONE) session_start();

        if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "entreprise") {
            header("Location: /");
            exit();
        }

        $generalController = new GeneralController();
        $profile_pic_path = $generalController->checkprofilepic($_SESSION["user_id"]);

        isset($_SESSION["flash_id_offer"]) ? $id_offer = $_SESSION["flash_id_offer"] : $id_offer = null;
        unset($_SESSION["flash_id_offer"]);

        if ($editmode) {
            $offerModel = new Offer();
            $offer = $offerModel->getOfferById($id_offer);
            $domains = $offerModel->getAllDomains();
        }
        else {
            $offerModel = new Offer();
            $domains = $offerModel->getAllDomains();
        }
        View::render("create_offer.twig", ["role" => $_SESSION["role"], "csrf_token" => Csrf::generateToken(), "domains" => $domains, "editmode" => $editmode, "offer" => $offer ?? null, 'profile_pic_path' => $profile_pic_path]);
    }


    public function createOffer() {
        if (session_status() === PHP_SESSION_NONE) session_start();

        Csrf::verifyToken($_POST["csrf_token"]);

        $offerModel = new Offer();
        $offerModel->create_offer($_SESSION["user_id"], $_POST["domain"], $_POST["offer_object"], $_POST["place"], $_POST["annual_salary"], $_POST["description"]);

        header("Location: /create_offer");
        exit();
    }
    
    public function editOffer() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $_SESSION["flash_id_offer"] = $_POST["id_offer"];
        $this->renderCreateOffer(true);
    }

    public function editOfferSave() {
        if (session_status() === PHP_SESSION_NONE) session_start();

        Csrf::verifyToken($_POST["csrf_token"]);

        $offerModel = new Offer();
        $offerModel->editOffer($_POST["id_offer"], $_POST["domain"], $_POST["offer_object"], $_POST["lieu"], $_POST["annual_salary"], $_POST["description"]);

        header("Location: /offer_description?id_offer=" . $_POST["id_offer"]);
        exit();
    }

 
}