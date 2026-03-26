<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Core\Router;
use Core\View;

$router = new Router();

/* ROUTES GÉNÉRALES */
$router->get("/", "AuthController@renderLoginRegister");
$router->post("/loginregister", "AuthController@form_type");

$router->post("/delete_account", "AuthController@deleteAccount");
$router->get("/delete_account", "AuthController@deleteAccount");

$router->get("/logout", "AuthController@logout");

$router->get("/profile", "ProfileController@renderProfile");
$router->get("/edit_profile", "ProfileController@editProfile");
$router->post("/modify_profile", "ProfileController@modifyProfile");

$router->get("/boo", "HomeController@boo");


$router->get("/search_page", "SearchPageController@renderingSearchPage");
$router->get("/search_companies", "SearchPageController@searchCompanies");

$router->dispatch();








