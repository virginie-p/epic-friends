<?php 

namespace App\Model;

class AdminManager extends Manager {
    public function deleteUser(int $id) {
        $db = $this->MySQLConnect();
        $req = $db->prepare('DELETE FROM project_5_users_parameters WHERE id = :id');

        $result = $req->execute(['id' => $id]);

        return $result;
    }
}