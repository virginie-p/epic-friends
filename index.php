<?php

require 'composer/vendor/autoload.php';
use App\Router\Router;
use App\Controller\HomeController;
use App\Controller\UserController;

$router = new Router($_GET['url']);

$router->addRoute('GET', '/', function(){ 
    $home_controller = new HomeController();
    $home_controller->displayDisconnectedHome();
});

$router->addRoute('GET|POST', '/subscribe', function(){
    $user_controller = new UserController();
    $user_controller->createUser();
});

$router->addRoute('GET', '/test', function(){echo 'Page de Test';});

$router->run();