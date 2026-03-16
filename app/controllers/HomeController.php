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
    
    public function boo1() {
        View::render("boo1.twig");
    }

    public function boo2() {
        View::render("boo2.twig");
    }

    public function boo3() {
        View::render("boo3.twig");
    }

}