<?php
namespace App\Controllers;

use Core\Csrf;
use Core\Auth;
use Core\View;

use App\Models\Pilot;

class MyStudentsController {

    public function renderMyStudents() {
        if (session_status() === PHP_SESSION_NONE) session_start();

        if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "pilote") {
            header("Location: /");
            exit();
        }

        $pilotModel = new Pilot();
        $students = $pilotModel->getStudentsByPilot($_SESSION["user_id"]);

        View::render("my_students.twig", ['students' => $students, 'role' => $_SESSION['role']]);
    }
}