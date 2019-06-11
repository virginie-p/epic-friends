<?php
namespace App\Controller;
use App\Model\UserManager;

class ResearchController extends Controller {

    public function displaySearchEngine() {
        $user_manager = new UserManager();
        $interests_center = $user_manager->getInterests();
        echo $this->twig->render('/front/searchEngine.twig', ['interests' => $interests_center]);
    }

}
