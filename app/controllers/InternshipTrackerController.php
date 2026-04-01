<?php
namespace App\Controllers;

use Core\Csrf;
use Core\Auth;
use Core\View;

use App\Models\Application;

class InternshipTrackerController {

    public function renderInternshipTracker() {
        if (session_status() === PHP_SESSION_NONE) session_start();

        if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "etudiant") {
            header("Location: /");
            exit();
        }

        $limit = 12;
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $offset = ($page - 1) * $limit;

        $params = $_GET;

        $next_page_url = "/internship_tracker?page=" . ($page + 1);
        $prev_page_url = "/internship_tracker?page=" . ($page - 1);

        $applicationModel = new Application();
        $cards = $applicationModel->getApplicationsByStudents($_SESSION["user_id"], $limit, $offset);
        $total_cards = $applicationModel->countApplicationsByStudents($_SESSION["user_id"]);

        $total_pages = ceil($total_cards / $limit);
        
        View::render("internship_tracker.twig", ['cards' => $cards, 'total_cards' => $total_cards, 'page' => $page, 'total_pages' => $total_pages, 'next_page_url' => $next_page_url, 'prev_page_url' => $prev_page_url, 'role' => $_SESSION['role']]);
    }
}