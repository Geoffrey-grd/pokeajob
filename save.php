<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Core\Router;
use Core\View;

$router = new Router();

/* ROUTES GÉNÉRALES */
$router->get("/", "HomeController@index"); // ou une page d'accueil

$router->dispatch();







<!DOCTYPE html>
    <html>

        <head>
            <title>Mon projet</title>
        </head>

        <body>
            <h1>{{ message }}</h1>

            {% for user in users %}
            <h2>{{ user.email }}</h2>
            <p>{{ user.password }}</p>
            {% endfor %}
            
        </body>
    </html> 












<?php

namespace App\Models;

require_once __DIR__ . "/../../config/database.php";

class Test {
    public function getAll($conn) {

        $sql = "SELECT * FROM user";

        return $conn->query($sql);
    }
}












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