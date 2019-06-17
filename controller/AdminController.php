<?php

namespace App\Controller;
use App\Entity\User;
use App\Model\UserManager;
use App\Model\ReportManager;

class AdminController extends Controller {
    public function displayAdmin() {
        if (isset($_SESSION['user']) && ($_SESSION['user']->userTypeId() == 4 || $_SESSION['user']->userTypeId() == 3)){
            $user_manager = new UserManager();
            $members = $user_manager->getMembers();

            echo $this->twig->render('/back/admin.twig', ['members' => $members]);
        }
        else {
            echo $this->twig->render('/front/homepage/disconnectedHome.twig');
        }
    }

    public function displayReports($member_id) {
        if (isset($_SESSION['user']) && ($_SESSION['user']->userTypeId() == 4 || $_SESSION['user']->userTypeId() == 3)){
            $report_manager = new ReportManager();
            $member_reports = $report_manager->getMemberReports($member_id);

            echo $this->twig->render('/back/memberReports.twig', [
                'member_reports' => $member_reports,
                'member_id' => $member_id
                ]);
        }
        else {
            echo $this->twig->render('/front/homepage/disconnectedHome.twig');

        }
    }
}