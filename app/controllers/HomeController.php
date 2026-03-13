<?php
namespace App\Controllers;

use App\Models\Test;
use Core\Auth;
use Core\Csrf;
use Core\View;
require_once __DIR__ . "/../../config/database.php";



class HomeController {

    public function index() {
        global $conn;

        $test = new Test();

        View::render("home.twig", [
            "message" => "Bienvenue sur mon nouveau projet",
            "users" => $test->getAll($conn)
        ]);

    }

    
}