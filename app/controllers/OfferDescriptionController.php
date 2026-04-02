<?php
namespace App\Controllers;

use Core\Csrf;
use Core\Auth;
use Core\View;
use Parsedown;

use App\Models\Offer;
use App\Models\Application;

use App\Controllers\GeneralController;

class OfferDescriptionController {

    public function renderOfferDescription() {
        if (session_status() === PHP_SESSION_NONE) session_start();

        if (!isset($_SESSION["user_id"])){
            header("Location: /");
            exit();
        }

        $generalController = new GeneralController();
        $profile_pic_path = $generalController->checkprofilepic($_SESSION["user_id"]);

        $offerModel = new Offer();
        $parsedown = new Parsedown();

        $is_owner = false;
        $isinwishlist = false;
        $hasapplied = false;

        $offer = $offerModel->getOfferById($_GET['id_offer']);
        $description = $parsedown->text($offer['description']);

        if ($offer['id_company'] === $_SESSION['user_id']) {
            $is_owner = true;
        }

        if ($_SESSION['role'] === 'etudiant') {
            $hasapplied = $this->hasApplied($_GET['id_offer']);
            $isinwishlist = $this->isinwishlist($_GET['id_offer']);
        }

        View::render("offer_description.twig", ["role" => $_SESSION["role"], "offer" => $offer, "description" => $description, "is_owner" => $is_owner, "isinwishlist" => $isinwishlist, "hasapplied" => $hasapplied, 'profile_pic_path' => $profile_pic_path]);
    }

    public function hasApplied($offer_id) {
        if (session_status() === PHP_SESSION_NONE) session_start();

        $application_model = new Application();
        $getapplication = $application_model->getApplication($_SESSION["user_id"], $offer_id);
        $hasapplied = !empty($getapplication);
        return $hasapplied;
    }

    public function isinwishlist($offer_id) {
        if (session_status() === PHP_SESSION_NONE) session_start();

        $offerModel = new Offer();
        $getfromwishlist = $offerModel->getFromWishlist($_SESSION["user_id"], $offer_id);
        $isinwishlist = !empty($getfromwishlist);
        return $isinwishlist;
    }

    public function addToWishlist() {
        if (session_status() === PHP_SESSION_NONE) session_start();

        $offerModel = new Offer();
        $offerModel->addToWishlist($_SESSION["user_id"], $_GET["id_offer"]);

        header("Location: /offer_description?id_offer=" . $_GET["id_offer"]);
        exit();
    }

    public function removeFromWishlist() {
        if (session_status() === PHP_SESSION_NONE) session_start();

        $offerModel = new Offer();
        $offerModel->removeFromWishlist($_SESSION["user_id"], $_GET["id_offer"]);

        header("Location: /offer_description?id_offer=" . $_GET["id_offer"]);
        exit();
    }

    public function deleteOffer() {
        if (session_status() === PHP_SESSION_NONE) session_start();

        $offerModel = new Offer();
        $this->deleteFile($_POST["id_offer"]);
        $offerModel->deleteOffer($_POST["id_offer"]);

        header("Location: /my_offers");
        exit();
    }

    public function deleteFile($id_offer) {
        $base_dir_cv = realpath(__DIR__ . "/../../public/images/uploads/application/cv") . "/";
        $base_dir_motivation_letter = realpath(__DIR__ . "/../../public/images/uploads/application/motivation_letter") . "/";

        foreach (glob($base_dir_cv . "*" . "offer" . $id_offer . ".*") as $existing_file) {
            unlink($existing_file);
        }

        foreach (glob($base_dir_motivation_letter . "*" . "offer" . $id_offer . ".*") as $existing_file) {
            unlink($existing_file);
        }
    }
}