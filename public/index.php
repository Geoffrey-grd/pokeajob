<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Core\Router;
use Core\View;

$router = new Router();

/* ROUTES GÉNÉRALES */
$router->get("/", "AuthController@renderLoginRegister");

$router->post("/loginregister", "AuthController@form_type");

$router->get("/boo1", "HomeController@boo1");
$router->get("/boo2", "HomeController@boo2");
$router->get("/boo3", "HomeController@boo3");

$router->get("/offers-list", "OfferslistController@index");

$router->dispatch();








