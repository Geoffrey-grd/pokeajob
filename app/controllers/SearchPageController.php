<?php
namespace App\Controllers;

use App\Models\Offer;
use App\Models\Companies;
use Core\Csrf;
use Core\Auth;
use Core\View;
require_once __DIR__ . "/../../config/database.php";



class SearchPageController {

    public function renderingSearchPage() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        global $conn;
        
        $limit = 9;
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $offset = ($page - 1) * $limit;
        $search_type = isset($_GET['search_type']) ? $_GET['search_type'] : 'offers';

        $params = $_GET;

        $next_page_url = $this->seturl("next_page_url", $params, $page, $search_type);
        $prev_page_url = $this->seturl("prev_page_url", $params, $page, $search_type);
        $companies_btn_url = $this->seturl("companies_btn_url", $params, $page, $search_type);
        $offers_btn_url = $this->seturl("offers_btn_url", $params, $page, $search_type);

        //var_dump($limit, $page, $offset, $search_type);
        if ($search_type === 'offers') {
            $cards = Offer::getAllOffers($conn, $limit, $offset);
            $total_cards = Offer::countOffers($conn);
            $total_pages = ceil($total_cards / $limit);
            //var_dump($cards, $total_cards);
        } else {
            $cards = Companies::getCompanies($conn);
            $total_cards = Companies::countCompanies($conn);
            $total_pages = ceil($total_cards / $limit);
        }

        //var_dump($total_cards, $page, $total_pages, $next_page_url, $prev_page_url, $companies_btn_url, $offers_btn_url);
        View::render("search_page.twig", ['search_type' => $search_type, 'cards' => $cards, 'total_cards' => $total_cards, 'page' => $page, 'total_pages' => $total_pages, 'next_page_url' => $next_page_url, 'prev_page_url' => $prev_page_url, 'companies_btn_url' => $companies_btn_url, 'offers_btn_url' => $offers_btn_url, 'role' => $_SESSION['role']]);
    }

    public function seturl($link, $params, $page, $search_type) {

        if ($link == "next_page_url") {
            $params['page'] = $page + 1; $params['search_type'] = $search_type;
            $url = "?" . http_build_query($params);
        }
        else if ($link == "prev_page_url") {
            $params['page'] = $page - 1; $params['search_type'] = $search_type;
            $url = "?" . http_build_query($params);
        }
        else if ($link == "companies_btn_url") {
            $params['search_type'] = 'companies'; $params['page'] = $page;
            $url = "?" . http_build_query($params);
        }
        else if ($link == "offers_btn_url") {
            $params['search_type'] = 'offers'; $params['page'] = $page;
            $url = "?" . http_build_query($params);
        }

        return $url;
    }
}