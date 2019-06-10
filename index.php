<?php

require 'composer/vendor/autoload.php';
use App\Router\Router;
use App\Controller\Controller;
use App\Controller\HomeController;
use App\Controller\UserController;

session_start();

$router = new Router($_GET['url']);

$router->addRoute('GET', '/404-not-found', function(){
    $controller = new Controller();
    $controller->display404();
});

$router->addRoute('GET', '/', function(){ 
    $home_controller = new HomeController();
    $home_controller->displayHome();
});

$router->addRoute('GET|POST', '/subscribe', function(){
    $user_controller = new UserController();
    $user_controller->createUser();
});

$router->addRoute('GET|POST', '/connection', function() {
    $user_controller = new UserController();
    $user_controller->connectUser();
});

$router->addRoute('GET', '/disconnect', function() {
    $user_controller = new UserController();
    $user_controller->disconnectUser();
});

$router->addRoute('GET', '/member/:id', function($id) {
    $user_controller = new UserController();
    $user_controller->displayProfile($id);
});

$router->addRoute('GET', '/my-profile', function(){
    $user_controller = new UserController();
    $user_controller->displayUserProfile();
});

$router->addRoute('GET|POST', '/modify-profile/:id', function($id) {
    $user_controller = new UserController();
    $user_controller->modifyProfile($id);
});

$router->addRoute('GET', 'my-account', function(){
    $user_controller = new UserController();
    $user_controller->displayAccount();
});

$router->addRoute('GET|POST', 'modify-account/:id', function($id) {
    $user_controller = new UserController();
    $user_controller->modifyAccount($id);
});

$router->addRoute('GET', '/test', function(){echo 'Page de Test';});

$router->run();