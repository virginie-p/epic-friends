<?php 

namespace App\Model;
use App\Entity\User;
use \PDO;

class UserManager extends Manager {

    public function addMember(User $user) {
        $db = $this->MySQLConnect();
        $req = $db->prepare('INSERT INTO project_5_users_parameters(
                                            user_type_id,
                                            username,
                                            password,
                                            email)
                            VALUES (:user_type_id,
                                    :username, 
                                    :password,
                                    :email)'
                            );

        $affected_lines = $req->execute([
            'user_type_id' => $user->userType(),
            'username' => $user->username(),
            'password' => $user->password(),
            'email' => $user->email()
        ]);

        if (!$affected_lines) {
            return false;
        }
        else {
            $last_id = $db->lastInsertId();
            
            $req = $db->prepare('INSERT INTO project_5_users_profiles(
                                                user_id,
                                                birthdate,
                                                profile_picture)
                                VALUES (:user_id,
                                        :birthdate,
                                        :profile_picture)'
                                );
            $affected_lines = $req->execute([
                'user_id' => $last_id,
                'birthdate' => $user->birthdate(),
                'profile_picture'=> $user->profilePicture()
            ]);
        }
        return $affected_lines;
    }

    public function getMembers() {
        $db = $this->MySQLConnect();
        $req = $db->query('SELECT   users_parameters.id,
                                    user_type_id,
                                    username,
                                    password,
                                    firstname,
                                    lastname,
                                    email,
                                    profile_picture,
                                    description, 
                                    DATE_FORMAT(creation_date, \'%d/%m/%Y à %Hh%i\') AS creation_date
                            FROM project_5_users_parameters AS users_parameters
                            INNER JOIN project_5_users_profiles AS users_profiles
                            ON users_parameters.id = users_profiles.user_id
                            WHERE user_type_id = 5');

        $req->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'App\Entity\User');

        $members = $req->fetchAll();

        return $members;
    }

    public function getUser($username) {
        $db = $this->MySQLConnect();
        $req = $db->prepare('SELECT users_parameters.id,
                                    user_type_id,
                                    username,
                                    password,
                                    email,
                                    firstname,
                                    lastname,
                                    birthdate,
                                    gender,
                                    county,
                                    favorite_citation,
                                    profile_picture,
                                    profile_banner,
                                    description,
                                    identified_as,
                                    DATE_FORMAT(creation_date, \'%d/%m/%Y à %Hh%i\') AS creation_date
                            FROM project_5_users_parameters AS users_parameters
                            INNER JOIN project_5_users_profiles AS users_profiles
                            ON users_parameters.id = users_profiles.user_id
                            WHERE username = ?');
        $req->execute(array($username));

        $req->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'App\Entity\User');

        $user = $req->fetch();

        return $user;
    }

    public function getCountyNewMembers($user) {
        $db = $this->MySQLConnect();
        $req = $db->prepare('SELECT users_parameters.id,
                                    user_type_id,
                                    username,
                                    password,
                                    email,
                                    firstname,
                                    lastname,
                                    birthdate,
                                    gender,
                                    county,
                                    favorite_citation,
                                    profile_picture,
                                    profile_banner,
                                    description,
                                    identified_as,
                                    DATE_FORMAT(creation_date, \'%d/%m/%Y à %Hh%i\') AS creation_date
                            FROM project_5_users_parameters AS users_parameters
                            INNER JOIN project_5_users_profiles AS users_profiles
                            ON users_parameters.id = users_profiles.user_id
                            WHERE county = :county AND user_parameters.id != :user_id');
        $req->execute([
            'county' => $user->county(),
            'user_id' => $user->id()
        ]);

        $req->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'App\Entity\User');

        $members = $req->fetchAll();

        return $members;
    }

    public function getNewMembers($user) {
        $db = $this->MySQLConnect();
        $req = $db->prepare('SELECT users_parameters.id,
                                    user_type_id,
                                    username,
                                    password,
                                    email,
                                    firstname,
                                    lastname,
                                    birthdate,
                                    gender,
                                    county,
                                    favorite_citation,
                                    profile_picture,
                                    profile_banner,
                                    description,
                                    identified_as,
                                    DATE_FORMAT(creation_date, \'%d/%m/%Y à %Hh%i\') AS creation_date
                            FROM project_5_users_parameters AS users_parameters
                            INNER JOIN project_5_users_profiles AS users_profiles
                            ON users_parameters.id = users_profiles.user_id
                            WHERE users_parameters.id != ?');
        $req->execute(array($user->id()));

        $req->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'App\Entity\User');

        $members = $req->fetchAll();

        return $members;
    }

    public function getMember($id) {
        $db = $this->MySQLConnect();
        $req = $db->prepare('SELECT users_parameters.id,
                                    user_type_id,
                                    username,
                                    password,
                                    email,
                                    firstname,
                                    lastname,
                                    birthdate,
                                    gender,
                                    county,
                                    favorite_citation,
                                    GROUP_CONCAT(interest_name SEPARATOR ", ") AS interests,
                                    profile_picture,
                                    profile_banner,
                                    description,
                                    identified_as,
                                    DATE_FORMAT(users_parameters.creation_date, "%d/%m/%Y à %Hh%i") AS creation_date
                            FROM project_5_users_parameters AS users_parameters
                            INNER JOIN project_5_users_profiles AS users_profiles ON users_parameters.id = users_profiles.user_id
                            INNER JOIN project_5_users_interests AS users_interests ON users_parameters.id = users_interests.user_id
                            INNER JOIN project_5_interests AS interests ON users_interests.interest_id = interests.id
                            WHERE users_parameters.id = ?
                            GROUP BY (users_parameters.id)');
        $req->execute(array($id));

        $req->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'App\Entity\User');

        $user = $req->fetch();

        return $user;
    }


}