<?php
namespace App\Model;
use App\Entity\User;
use \PDO;

class SearchManager extends Manager {
    
    public function searchMembers($research, $start_from_user, $number_of_users, $connected_user_id) {
       try {      
            $db= $this->MySQLConnect();
            $query = 'SELECT users_parameters.id, 
                            gender,
                            county,
                            username,
                            GROUP_CONCAT(interest_name SEPARATOR ", ") AS interests, 
                            birthdate, 
                            profile_picture
                    FROM project_5_users_parameters AS users_parameters
                    LEFT JOIN project_5_users_profiles AS users_profiles ON users_parameters.id = users_profiles.user_id
                    LEFT JOIN project_5_users_interests AS users_interests ON users_parameters.id = users_interests.user_id
                    LEFT JOIN project_5_interests AS interests_names ON users_interests.interest_id = interests_names.id
                    WHERE (users_parameters.id != :connected_user_id AND (birthdate BETWEEN :start_birthdate AND :end_birthdate) AND user_type_id = 5';

            if(!is_null($research->gender())) {
                $query .= ' AND gender = :gender';
            }

            if(!is_null($research->county())) {
                $query .= ' AND county = :county';
            }

            if(!empty($research->interests())) {
                foreach ($research->interests() as $key => $value) {
                    if ($key === 0) {
                        $query .= ' AND (interest_id = :interest' . $key;
                    }
                    else  {
                        $query .= ' OR interest_id = :interest' . $key;
                    }
                }
                $query .= ')';
            }

            $query .= ') GROUP BY users_parameters.id LIMIT :start_from_user, :number_of_users';

            $req = $db->prepare($query);

            $req->bindValue('connected_user_id', $connected_user_id, PDO::PARAM_INT);
            $req->bindValue('start_birthdate', $research->maxBirthday(), PDO::PARAM_STR);
            $req->bindValue('end_birthdate', $research->minBirthday(), PDO::PARAM_STR);
            $req->bindValue('start_from_user', $start_from_user, PDO::PARAM_INT);
            $req->bindValue('number_of_users', $number_of_users, PDO::PARAM_INT);
            if(!is_null($research->county())) {
                $req->bindValue('county', $research->county(), PDO::PARAM_STR);
            }
            if(!is_null($research->gender())) {
                $req->bindValue('gender', $research->gender(), PDO::PARAM_STR);

            }
            if(!empty($research->interests())) {
                foreach ($research->interests() as $key => $value) {
                    $req->bindValue('interest' . $key, $value, PDO::PARAM_INT);
                }
            }

            $req->execute();
            $req->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'App\Entity\User');
            $result = $req->fetchAll();

            return $result;
       }
       catch (\PDOException $pdoE) {
           throw new \Exception($pdoE->getMessage());
       }    
    }

    public function countSearchedMembers($research, $connected_user_id){
        $db= $this->MySQLConnect();
        $query = 'SELECT COUNT(*) FROM (SELECT users_parameters.id
                FROM project_5_users_parameters AS users_parameters
                LEFT JOIN project_5_users_profiles AS users_profiles ON users_parameters.id = users_profiles.user_id
                LEFT JOIN project_5_users_interests AS users_interests ON users_parameters.id = users_interests.user_id
                WHERE users_parameters.id != :connected_user_id AND (birthdate BETWEEN :start_birthdate AND :end_birthdate) AND user_type_id = 5';
        
        $execute = ['connected_user_id' => $connected_user_id, 
                    'start_birthdate'=> $research->maxBirthday(),
                    'end_birthdate'=> $research->minBirthday()];

        if(!is_null($research->gender())) {
            $query .= ' AND gender = :gender';
            $execute['gender'] = $research->gender();
        }

        if(!is_null($research->county())) {
            $query .= ' AND county = :county';
            $execute['county'] = $research->county();
        }

        if(!empty($research->interests())) {
            foreach ($research->interests() as $key => $value) {
                if ($key === 0) {
                    $query .= ' AND (interest_id = :interest' . $key;
                }
                else  {
                    $query .= ' OR interest_id = :interest' . $key;
                }
                $execute['interest' . $key] =  $value;
            }
            $query .= ')';
        }

        $query .= ' GROUP BY users_parameters.id) AS data';

        $req = $db->prepare($query);
        $test = $req->execute($execute);
        $result = $req->fetch();

        return $result;
    }
}