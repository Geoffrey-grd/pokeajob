<?php
namespace App\Controllers;

use Core\Csrf;
use Core\Auth;
use Core\View;
use Parsedown;

use App\Models\Offer;


class OfferDescriptionController {

    public function renderOfferDescription() {
        if (session_status() === PHP_SESSION_NONE) session_start();

        if (!isset($_SESSION["user_id"])){
            header("Location: /");
            exit();
        }

        $offerModel = new Offer();
        $parsedown = new Parsedown();

        $offer = $offerModel->getOfferById($_GET['id_offer']);
        $description = $parsedown->text($offer['description']);


        View::render("offer_description.twig", ["role" => $_SESSION["role"], "offer" => $offer, "description" => $description]);
    }

}