<?php
namespace App\Controller;

class HomeController extends Controller {

    public function displayHome() {
        
        if(isset($_SESSION['user'])) {
            $user_controller = new UserController;
            $new_users = $user_controller->getNewMembers();

            echo $this->twig->render('front/homepage/connectedHome.twig', ['new_users' => $new_users]);
        }
        else {
            echo $this->twig->render('/front/homepage/disconnectedHome.twig');
        }
    }
}