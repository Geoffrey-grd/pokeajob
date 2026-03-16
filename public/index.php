<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Core\Router;
use Core\View;

$router = new Router();

/* ROUTES GÉNÉRALES */
$router->get("/", "AuthController@login");

$router->post("/loginregister", "AuthController@form_type");

$router->get("/boo", "HomeController@boo");

$router->dispatch();








