<?php
namespace App\Controller;

class HomeController extends Controller {

    public function displayDisconnectedHome() {
        // $testimony_manager = new TestimonyManager();
        // $testimony_manager->getTop3Comments();

        echo $this->twig->render('/front/homepage/disconnectedHome.twig');
    }
}