<?php
namespace App\Controller;
use App\Model\UserManager;
use App\Entity\User;

class HomeController extends Controller {

    public function displayHome() {
        
        if(isset($_SESSION['user'])) {
            $user_controller = new UserController();
            $new_users = $user_controller->getNewMembers();

            echo $this->twig->render('front/homepage/connectedHome.twig', ['new_users' => $new_users]);
        }
        else {
            $user_manager = new UserManager();
            $geek_sample = $user_manager->getRandomMembers();
            
            echo $this->twig->render('/front/homepage/disconnectedHome.twig',['geek_sample' => $geek_sample]);
        }
    }
}