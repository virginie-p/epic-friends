<?php

require 'composer/vendor/autoload.php';
require 'config.php';
use App\Router\Router;
use App\Controller\Controller;
use App\Controller\HomeController;
use App\Controller\UserController;
use App\Controller\ResearchController;
use App\Controller\MailboxController;
use App\Controller\AdminController;
use App\Controller\ReportController;

date_default_timezone_set(TIMEZONE);

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

$router->addRoute('GET', '/my-account', function(){
    $user_controller = new UserController();
    $user_controller->displayAccount();
});

$router->addRoute('GET|POST', '/modify-account/:id', function($id) {
    $user_controller = new UserController();
    $user_controller->modifyAccount($id);
});

$router->addRoute('GET', '/search-engine', function() {
    $research_controller = new ResearchController();
    $research_controller->displaySearchEngine();
});

$router->addRoute('GET|POST', '/search-members', function(){
    $research_controller = new ResearchController();
    $research_controller->displaySearchResults();
});

$router->addRoute('GET|POST', '/searched-members/p/:page_number', function($page_number) {
    $research_controller = new ResearchController();
    $research_controller->displayMoreResults($page_number);
});

$router->addRoute('GET', '/mailbox', function(){
    $mailbox_controller = new MailboxController();
    $mailbox_controller->displayMailbox();
});

$router->addRoute('GET|POST', '/send-message/user/:id', function($id){
    $mailbox_controller = new MailboxController();
    $mailbox_controller->sendMessage($id);
});

$router->addRoute('GET', '/get-new-messages/:user_id/from-message/:last_message_id', function($user_id, $last_message_id) {
    $mailbox_controller = new MailboxController();
    $mailbox_controller->getUserNewMessages($user_id, $last_message_id);
});

$router->addRoute('GET', '/display-messages/:member_id', function($member_id) {
    $mailbox_controller = new MailboxController();
    $mailbox_controller->displayMessages($member_id);
});

$router->addRoute('GET', '/get-unread-messages', function() {
    $mailbox_controller = new MailboxController(); 
    $mailbox_controller->getNewMessages();
});

$router->addRoute('GET', '/users-management', function() {
    $admin_controller = new AdminController();
    $admin_controller->displayUsersManagement();
});

$router->addRoute('GET|POST', '/report-member/:id', function($id){
    $report_controller = new ReportController();
    $report_controller->reportMember($id);
});

$router->addRoute('GET', '/display-reports/:member_id', function($member_id) {
    $admin_controller = new AdminController();
    $admin_controller->displayReports($member_id);
});

$router->addRoute('GET', '/delete/user/:id', function($id) {
    $admin_controller = new AdminController();
    $admin_controller->deleteUser($id);
});

$router->addRoute('GET', '/interests-management', function(){
    $admin_controller = new AdminController();
    $admin_controller->displayInterests();
});

$router->run();