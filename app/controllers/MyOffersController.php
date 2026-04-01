<?php
namespace App\Controllers;

use Core\Csrf;
use Core\Auth;
use Core\View;

use App\Models\Offer;

class MyOffersController {

    public function renderMyOffers() {
        if (session_status() === PHP_SESSION_NONE) session_start();

        if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "entreprise") {
            header("Location: /");
            exit();
        }

        $limit = 12;
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $offset = ($page - 1) * $limit;
        $filter = isset($_GET['filter']) && $_GET['filter'] !== '' ? $_GET['filter'] : null;

        $params = $_GET;

        $next_page_url = $this->seturl("next_page_url", $params, $page, $filter);
        $prev_page_url = $this->seturl("prev_page_url", $params, $page, $filter);

        $offerModel = new Offer();
        if ($filter) {
                $cards = $offerModel->getFilteredOffersByCompany($_SESSION["user_id"], $limit, $offset, $filter);
                $total_cards = $offerModel->countFilteredOffersByCompany($_SESSION["user_id"], $filter);
            } 
        else {
            $cards = $offerModel->getOffersByCompany($_SESSION["user_id"], $limit, $offset);
            $total_cards = $offerModel->countOffersByCompany($_SESSION["user_id"]);
        }

        $total_pages = ceil($total_cards / $limit);
        $filters = $offerModel->getAllDomains();

        View::render("my_offers.twig", ['filters' => $filters, 'cards' => $cards, 'total_cards' => $total_cards, 'page' => $page, 'total_pages' => $total_pages, 'next_page_url' => $next_page_url, 'prev_page_url' => $prev_page_url, 'role' => $_SESSION['role']]);
    }




    public function seturl($link, $params, $page, $filter) {
        if ($link == "next_page_url") {
            $params['page'] = $page + 1; $params['filter'] = $filter;
            $url = "?" . http_build_query($params);
        }
        else if ($link == "prev_page_url") {
            $params['page'] = $page - 1; $params['filter'] = $filter;
            $url = "?" . http_build_query($params);
        }
        return $url;
    }
}


