<?php

namespace App\Router;

class Router {

    private $_url;
    private $_routes = [];


    public function __construct($url) {
        $this->_url = $url;
    }

    public function addRoute($methods, $path, $toCallFunction) {
        $methods = explode('|', $methods);

        foreach ($methods as $method) {
            $route = new Route($path, $toCallFunction);

            $this->_routes[$method][] = $route;
        }
    }

    public function run() {
        if(!isset($this->_routes[$_SERVER['REQUEST_METHOD']])){
            throw new \Exception ('No ' . $_SERVER['REQUEST_METHOD'] . ' route existing');
        }
        foreach($this->_routes[$_SERVER['REQUEST_METHOD']] as $route) {
            if($route->match($this->_url)) {
                return $route->call();
            }
        }
        
        header("HTTP/1.0 404 Not Found");
    }

}