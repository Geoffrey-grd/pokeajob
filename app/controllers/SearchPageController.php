<?php
namespace App\Controllers;

use App\Models\Offer;
use App\Models\Company;
use Core\Csrf;
use Core\Auth;
use Core\View;

use App\Controllers\GeneralController;


class SearchPageController {

    public function renderingSearchPage() {
        if (session_status() === PHP_SESSION_NONE) session_start();

        if (!isset($_SESSION["user_id"])) {
            header("Location: /");
            exit();
        }

        $generalController = new GeneralController();
        $profile_pic_path = $generalController->checkprofilepic($_SESSION["user_id"]);

        $limit = 12;
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
            $offer = new Offer();
            if ($filter) {
                $cards = $offer->getFilteredOffers($limit, $offset, $filter);
                $total_cards = $offer->countFilteredOffers($filter);
            } 
            else {
                $cards = $offer->getAllOffers($limit, $offset);
                $total_cards = $offer->countOffers();
            }
            $total_pages = ceil($total_cards / $limit);
            $filters = $offer->getAllDomains();
        }
        else {
            $company = new Company();
            if ($filter) {
                $cards = $company->getFilteredCompanies($limit, $offset, $filter);
                $total_cards = $company->countFilteredCompanies($filter);
            }
            else {
                $cards = $company->getCompanies();
                $total_cards = $company->countCompanies();
            }
            $total_pages = ceil($total_cards / $limit);
            $filters = $company->getAllActivitySectors();
        }

        View::render("search_page.twig", ['filters' => $filters, 'search_type' => $search_type, 'cards' => $cards, 'total_cards' => $total_cards, 'page' => $page, 'total_pages' => $total_pages, 'next_page_url' => $next_page_url, 'prev_page_url' => $prev_page_url, 'companies_btn_url' => $companies_btn_url, 'offers_btn_url' => $offers_btn_url, 'role' => $_SESSION['role'], 'profile_pic_path' => $profile_pic_path]);
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
            $params['search_type'] = 'companies'; $params['page'] = 1; $params['filter'] = $filter;
            $url = "?" . http_build_query($params);
        }
        else if ($link == "offers_btn_url") {
            $params['search_type'] = 'offers'; $params['page'] = 1; $params['filter'] = $filter;
            $url = "?" . http_build_query($params);
        }
        return $url;
    }
}