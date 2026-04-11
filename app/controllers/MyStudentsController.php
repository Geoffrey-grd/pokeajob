<?php
namespace App\Controllers;

use Core\Csrf;
use Core\Auth;
use Core\View;

use App\Models\Pilot;

use App\Controllers\GeneralController;

class MyStudentsController {

    public function renderMyStudents() {
        if (session_status() === PHP_SESSION_NONE) session_start();

        if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "pilote") {
            header("Location: /");
            exit();
        }

        $generalController = new GeneralController();
        $profile_pic_path = $generalController->checkprofilepic($_SESSION["user_id"], $_SESSION["role"]);

        $pilotModel = new Pilot();
        $students = $pilotModel->getStudentsByPilot($_SESSION["user_id"]);

        View::render("my_students.twig", ['students' => $students, 'role' => $_SESSION['role'], 'profile_pic_path' => $profile_pic_path]);
    }
}