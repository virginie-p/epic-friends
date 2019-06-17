<?php
namespace App\Controller;
use App\Entity\User;
use App\Entity\Report;
use App\Model\UserManager;

class ReportController extends Controller {
    public function reportMember($id) {
        if (isset($_SESSION['user']) && $_SESSION['user']->userTypeId() == 5) {
            $user_manager = new UserManager();
            $user = $user_manager->getMember($id);

            if (!$user) {
                $data['status'] = 'error';
                $data['errors'] = ['user_not_found'];
                echo json_encode($data);
            }
            else {
                $report_data = [
                    'reported_user_id' => $id,
                    'informer_user_id' => $_SESSION['user']->id(),
                    'report_reason' => $_POST['report-reason']
                ];
                $report = new Report($report_data);
                $user_reported = $user_manager->reportMember($report);

                if(!$user_reported) {
                    $data['status'] = 'error';
                    $data['errors'] = ['user_not_reported'];
                    echo json_encode($data);
                } 
                else {
                    echo json_encode(['status' => 'success']);
                }
            }
        }
        else {
            echo $this->twig->render('/front/homepage/disconnectedHome.twig');
        }

    }

}