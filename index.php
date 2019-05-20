<?php

require 'composer/vendor/autoload.php';
use App\Router\Router;

$router = new Router($_GET['url']);
$router->addRoute('GET', '/', function(){ echo 'Homepage';});
$router->addRoute('GET', '/test', function(){echo 'Page de Test';});

$router->run();