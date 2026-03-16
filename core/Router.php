<?php
namespace Core;

class Router {

    private $routes = [];

    public function get($url, $action) {
        $this->routes["GET"][$url] = $action;
    }

    public function post($url, $action) {
        $this->routes["POST"][$url] = $action;
    }

    public function dispatch() {

        $httpMethod = $_SERVER["REQUEST_METHOD"];
        $uri = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);

        if (isset($this->routes[$httpMethod][$uri])) {
            $action = $this->routes[$httpMethod][$uri];
            list($controller, $method) = explode("@", $action);
            $controllerClass = "App\\Controllers\\" . $controller;
            $instance = new $controllerClass();
            $instance->$method();
        } else {
            http_response_code(404);
            echo "404 - Page non trouvée";
        }

    }
}   


