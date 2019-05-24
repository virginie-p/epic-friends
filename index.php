<?php

require 'composer/vendor/autoload.php';
use App\Router\Router;
use App\Controller\HomeController;

$router = new Router($_GET['url']);

$router->addRoute('GET', '/', function(){ 
    $home_controller = new HomeController();
    $home_controller->displayDisconnectedHome();
});

$router->addRoute('GET', '/test', function(){echo 'Page de Test';});

$router->run();