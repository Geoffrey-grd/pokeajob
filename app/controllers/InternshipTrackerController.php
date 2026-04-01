<?php
namespace App\Controllers;

use Core\Csrf;
use Core\Auth;
use Core\View;

use App\Models\Application;

class InternshipTrackerController {

    public function renderInternshipTracker() {
        if (session_status() === PHP_SESSION_NONE) session_start();

        if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "etudiant" && $_SESSION["role"] !== "pilote") {
            header("Location: /");
            exit();
        }

        isset($_GET["id_user"]) ? $id_user = $_GET["id_user"] : $id_user = $_SESSION["user_id"];
        isset($_GET["student_name"]) ? $student_name = $_GET["student_name"] : $student_name = "";
        isset($_GET["student_last_name"]) ? $student_last_name = $_GET["student_last_name"] : $student_last_name = "";

        $limit = 12;
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $offset = ($page - 1) * $limit;

        $next_page_url = "/internship_tracker?page=" . ($page + 1) . "&id_user=" . $id_user . "&student_name=" . $student_name . "&student_last_name=" . $student_last_name;
        $prev_page_url = "/internship_tracker?page=" . ($page - 1) . "&id_user=" . $id_user . "&student_name=" . $student_name . "&student_last_name=" . $student_last_name;

        $applicationModel = new Application();
        $cards = $applicationModel->getApplicationsByStudents($id_user, $limit, $offset);

        $total_pages = ceil(count($cards) / $limit);

        View::render("internship_tracker.twig", ['cards' => $cards, 'page' => $page, 'total_pages' => $total_pages, 'next_page_url' => $next_page_url, 'prev_page_url' => $prev_page_url, 'student_name' => $student_name, 'student_last_name' => $student_last_name, 'role' => $_SESSION['role']]);
    }
}
