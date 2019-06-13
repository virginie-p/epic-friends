<?php 

namespace App\Model;
use App\Entity\User;
use App\Entity\Interest;
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
        $req->execute([$username]);

        $req->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'App\Entity\User');

        $user = $req->fetch();

        return $user;
    }

    public function getNewCountyMembers($user) {
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
                            LEFT JOIN project_5_users_profiles AS users_profiles ON users_parameters.id = users_profiles.user_id
                            LEFT JOIN project_5_users_interests AS users_interests ON users_parameters.id = users_interests.user_id
                            LEFT JOIN project_5_interests AS interests_names ON users_interests.interest_id = interests_names.id
                            WHERE users_parameters.id = ?
                            GROUP BY (users_parameters.id)');
        $req->execute(array($id));

        $req->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'App\Entity\User');

        $user = $req->fetch();

        return $user;
    }

    public function getInterests() {
        $db = $this->MySQLConnect();
        $req = $db->query('SELECT id, interest_name FROM project_5_interests ORDER BY interest_name ASC');

        $req->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'App\Entity\Interest');
        $interests = $req->fetchAll();
        
        return $interests;
    }

    public function modifyProfile(array $user_data) {
        $db = $this->MySQLConnect();
        $req = $db->prepare('UPDATE project_5_users_profiles 
                            SET birthdate = :birthdate,
                                gender = :gender,
                                county = :county,
                                favorite_citation = :favorite_citation,
                                description = :description,
                                profile_picture = :profile_picture,
                                profile_banner = :profile_banner, 
                                identified_as = :identified_as
                            WHERE user_id = :user_id');
        $result = $req->execute([
            'birthdate' => $user_data['birthdate'],
            'gender' => $user_data['gender'],
            'county' => $user_data['county'],
            'favorite_citation' => $user_data['favorite_citation'],
            'description' => $user_data['description'],
            'profile_picture' => $user_data['profile_picture'],
            'profile_banner' => $user_data['profile_banner'],
            'identified_as' => $user_data['identified_as'],
            'user_id' => $user_data['id']
        ]);

        
        if (!$result) {
            return $result;

        } else {
            $second_req = $db->prepare('DELETE FROM project_5_users_interests WHERE user_id = :user_id');

            $result = $second_req->execute(['user_id' => $user_data['id']]);
            
            $second_req->closeCursor();

            if (!$result) {
                return $result;
            }
            else {
                foreach ($user_data['interests'] as $interest_id) {

                    $third_req = $db->prepare('INSERT INTO project_5_users_interests(user_id, interest_id) VALUES (:user_id, :interest_id)');
    
                    $result = $third_req->execute([
                        'interest_id' => $interest_id, 
                        'user_id' => $user_data['id']
                    ]);

                    $third_req->closeCursor();
                }
            }  

            return $result;
        }
        
    }

    public function modifyAccount(array $user_data) {
        $db = $this->MySQLConnect();
        $req = $db->prepare('UPDATE project_5_users_parameters
                            SET username = :username,
                                password = :password,
                                email = :email
                            WHERE id = :id');
        $result = $req->execute([
            'username' => $user_data['username'],
            'password' => $user_data['password'],
            'email' => $user_data['email'], 
            'id' => $user_data['id']
        ]);
        
        return $result;
    }
}