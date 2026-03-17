<?php
namespace App\Controllers;

use App\Models\Offer;
use Core\Csrf;
use Core\Auth;
use Core\View;
require_once __DIR__ . "/../../config/database.php";



class OfferslistController {

    public function index() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        global $conn;

        $offers = Offer::getAllOffers($conn);

        View::render("offers_list.twig", ['offers' => $offers, 'role' => $_SESSION['role']]);
    }
}