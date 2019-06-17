<?php
namespace App\Model;
use App\Entity\Report;
use \PDO;

class ReportManager extends Manager {
    public function getMemberReports(int $member_id) {
        $db = $this->MySQLConnect();
        $req = $db->prepare('SELECT * FROM project_5_users_reports WHERE reported_user_id = :member_id');

        $req->execute(['member_id' => $member_id]);
        $req->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'App\Entity\Report');

        $result = $req->fetchAll();

        return $result;
    }
}