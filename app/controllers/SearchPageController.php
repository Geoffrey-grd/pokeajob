<?php
namespace App\Controllers;

use App\Models\Offer;
use App\Models\Company;
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
        $search_type = isset($_SESSION['flash_search_type']) ? $_SESSION['flash_search_type'] : (isset($_GET['search_type']) ? $_GET['search_type'] : 'offers');
        $filter = isset($_GET['filter']) && $_GET['filter'] !== '' ? $_GET['filter'] : null;

        unset($_SESSION['flash_search_type']);

        $params = $_GET;

        $next_page_url = $this->seturl("next_page_url", $params, $page, $search_type, $filter);
        $prev_page_url = $this->seturl("prev_page_url", $params, $page, $search_type, $filter);
        $companies_btn_url = $this->seturl("companies_btn_url", $params, $page, $search_type, null);
        $offers_btn_url = $this->seturl("offers_btn_url", $params, $page, $search_type, null);

        if ($search_type === 'offers') {
            if ($filter) {
                $cards = Offer::getFilteredOffers($conn, $limit, $offset, $filter);
                $total_cards = Offer::countFilteredOffers($conn, $filter);
            } 
            else {
                $cards = Offer::getAllOffers($conn, $limit, $offset);
                $total_cards = Offer::countOffers($conn);
            }
            $total_pages = ceil($total_cards / $limit);
            $filters = Offer::getAllDomains($conn);
        }
        else {
            if ($filter) {
                $cards = Company::getFilteredCompanies($conn, $limit, $offset, $filter);
                $total_cards = Company::countFilteredCompanies($conn, $filter);
            }
            else {
                $cards = Company::getCompanies($conn);
                $total_cards = Company::countCompanies($conn);
            }
            $total_pages = ceil($total_cards / $limit);
            $filters = Company::getAllActivitySectors($conn);
        }

        View::render("search_page.twig", ['filters' => $filters, 'search_type' => $search_type, 'cards' => $cards, 'total_cards' => $total_cards, 'page' => $page, 'total_pages' => $total_pages, 'next_page_url' => $next_page_url, 'prev_page_url' => $prev_page_url, 'companies_btn_url' => $companies_btn_url, 'offers_btn_url' => $offers_btn_url, 'role' => $_SESSION['role']]);
    }

    public static function searchCompanies() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $_SESSION["flash_search_type"] = "companies";
        header("Location: /search_page");
        exit();
    }
    

    public function seturl($link, $params, $page, $search_type, $filter) {

        if ($link == "next_page_url") {
            $params['page'] = $page + 1; $params['search_type'] = $search_type; $params['filter'] = $filter;
            $url = "?" . http_build_query($params);
        }
        else if ($link == "prev_page_url") {
            $params['page'] = $page - 1; $params['search_type'] = $search_type; $params['filter'] = $filter;
            $url = "?" . http_build_query($params);
        }
        else if ($link == "companies_btn_url") {
            $params['search_type'] = 'companies'; $params['page'] = $page; $params['filter'] = $filter;
            $url = "?" . http_build_query($params);
        }
        else if ($link == "offers_btn_url") {
            $params['search_type'] = 'offers'; $params['page'] = $page; $params['filter'] = $filter;
            $url = "?" . http_build_query($params);
        }

        return $url;
    }
}