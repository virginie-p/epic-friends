<?php 

namespace App\Model;

use \PDO;
use App\Entity\Interest;

class AdminManager extends Manager {
    public function deleteUser(int $id) {
        $db = $this->MySQLConnect();
        $req = $db->prepare('DELETE FROM project_5_users_parameters WHERE id = :id');

        $result = $req->execute(['id' => $id]);

        return $result;
    }

    public function getInterests() {
        $db = $this->MySQLConnect();
        $req = $db->query('SELECT * FROM project_5_interests');

        $req->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'App\Entity\Interest');

        $result = $req->fetchAll(); 

        return $result;
    }

    public function deleteInterest(int $id){
        $db = $this->MySQLConnect();
        $req = $db->prepare('DELETE FROM project_5_interests WHERE id = :id');

        $result = $req->execute(['id' => $id]);

        return $result;
    }

    public function getInterest($id) {
        $db = $this->MySQLConnect();
        $req = $db->prepare('SELECT * FROM project_5_interests WHERE id = :id');

        $req->execute(['id' => $id]);
        $req->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'App\Entity\Interest');
        $result = $req->fetchAll(); 
        
        return $result;
    }
}