<?php

namespace App\Controller;
use App\Entity\User;
use App\Entity\Report;
use App\Model\UserManager;
use App\Model\ReportManager;
use Emojione\Client;
use Emojione\Ruleset;

class UserController extends Controller {

    public function createUser() {
        $user_data = [];
        $errors = [];

        if (!empty($_POST['subscribe-username']) && !empty($_POST['subscribe-password']) && !empty($_POST['password-confirmation']) 
            && !empty($_POST['email']) && !empty($_POST['birthdate']) && !empty($_FILES['profile-picture']['name'])) {
            $user_manager = new UserManager();
            $users = $user_manager->getMembers();

            foreach($users as $user) {
                if ($user->username() === $_POST['subscribe-username']) {
                    $errors[] = 'username_already_used';
                }
                if ($user->email() === $_POST['email']) {
                    $errors[] = 'email_already_used';
                }
            }

            if (!preg_match('#^[0-9A-Za-záàâäãåçéèêëíìîïñóòôöõúùûüýÿæœÁÀÂÄÃÅÇÉÈÊËÍÌÎÏÑÓÒÔÖÕÚÙÛÜÝŸÆŒ._-]{6,20}$#', $_POST['subscribe-username'])){
                $errors[] = 'username_not_matching_regex' ;
            }

            if ($_POST['subscribe-password'] != $_POST['password-confirmation']) {
                $errors[] = 'passwords_not_identical' ;
            }

            if (!preg_match('#^((?=\S*?[A-Z])(?=\S*?[a-z])(?=\S*?[0-9]).{7,})\S$#', $_POST['subscribe-password'])) {
                $errors[] = 'password_not_matching_regex';
            }

            if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'email_invalid';
            }

            $birthdate = \DateTime::createFromFormat('j/m/Y', $_POST['birthdate']);

            $actual_date = new \Datetime();
            $datediff = $actual_date->diff($birthdate);
            $age = $datediff->y;

            if($age < 18) {
                $errors[]='age_not_authorized';
            }
            
            $user_data = array(
                'user_type_id' => 5,
                'username' => $_POST['subscribe-username'],
                'password' => password_hash($_POST['subscribe-password'], PASSWORD_DEFAULT),
                'email' => $_POST['email'],
                'birthdate' => $birthdate->format('Y-m-d H:i:s')
            );

            $image_input_name = 'profile-picture';
                        
            if($_FILES[$image_input_name]['error'] != 0) {
                $errors[] = 'image_or_size_invalid';
            }

            if (empty($errors)) { 
                $upload_result = $this->createImageInFolder($image_input_name, profile_picture_width, profile_picture_height, profile_picture_folder);

                if($upload_result['status'] == 'success') {
                    $user_data['profile_picture'] = $upload_result['image_name'];
                }
                else {
                    foreach ($user_result['errors'] as $error){
                        $errors[] = $error;
                    }
                }
               
            }

            if(empty($errors)) {
                $new_user = new User($user_data);

                $affected_lines = $user_manager->addMember($new_user);

                if (!$affected_lines) {
                    $errors[] = 'upload_problem';
                } else {
                    $headers  = 'From: "Virginie PEREIRA" <contact@virginie-pereira.fr>' . "\r\n" .
                                'Reply-To: "Virginie PEREIRA" <contact@virginie-pereira.fr>' . "\r\n" .
                                'MIME-Version: 1.0' . "\r\n" .
                                'Content-type: text/html;  charset=utf-8'. "\r\n" .
                                'X-Mailer: PHP/' . phpversion();

                    $message =  '<html><body>'. "\r\n" .
                                '<img src="https://epic-friends.virginie-pereira.fr/public/images/logo.png" style="width:80px;height:80px"><span style="font-weight:bold">Epic Friends</span>'. "\r\n" .
                                '<p> Bonjour ' . $new_user->username() . ' !</p>'."\r\n" .
                                '<p> Vous êtes bien inscrit sur Epic Friends ! </p>'."\r\n" .
                                '<p> N\'hésitez pas à vous connecter très souvent sur le site pour'."\r\n" .
                                ' pouvoir discuter et vous faire de nouveaux amis ! :) </p>'."\r\n" .
                                '<p> A très bientôt ! </p>' ."\r\n" .
                                '</body></html>';

                    mail($new_user->email(), 'Votre inscription sur Epic Friends', $message, $headers);
                    echo json_encode([
                        'status' => 'success'
                    ]);
                }
            }
        }
        else {
            $errors[] = 'missing_fields';
        }

        if (!empty($errors)) {
            $data['status'] = 'error';
            $data['errors'] = $errors;
            echo json_encode($data);
        }
        
    }

    public function connectUser() {
        $user_manager = new UserManager();
        $user = $user_manager->getUser($_POST['username']);

        if ($user != false) {
            if (!password_verify($_POST['password'], $user->password())){
                echo json_encode([
                    'status' => 'error'
                ]);
            }
            else {
                $_SESSION['user'] = $user;
        
                echo json_encode([
                    'status' => 'success'
                ]);
            }
        } else {
            echo json_encode([
                'status' => 'error'
            ]);
        }

    }

    public function disconnectUser() {
        $_SESSION = [];
        session_destroy();

        header('Location: ' . BASE_URL);
        exit;
    }

    public function getNewMembers() {
        if(isset($_SESSION['user'])) {
            $user_manager = new UserManager();
            if ($_SESSION['user']->county() == NULL){
                $new_users = $user_manager->getNewMembers($_SESSION['user']);
            }
            else {
                $new_users = $user_manager->getNewCountyMembers($_SESSION['user']);
            }
            
            return $new_users;
            }
        else {
            $user_manager = new UserManager();
            $geek_sample = $user_manager->getRandomMembers();
            
            echo $this->twig->render('/front/homepage/disconnectedHome.twig',['geek_sample' => $geek_sample]);
        }
    }

    public function displayProfile($id) {
        if(isset($_SESSION['user'])) {
            $user_manager = new UserManager();
            $member = $user_manager->getMember($id);
            $report_manager = new ReportManager();
            $user_nbr_of_reports = $report_manager->getMemberReportsByUser($id, $_SESSION['user']->id());
            
            // var_dump($member);
            // die();

            if (!$member) {
                header('HTTP/1.0 404 Not Found');
                exit;
            } else {
                echo $this->twig->render('front/memberProfile.twig', [
                    'member' => $member,
                    'member_reports_by_user' => $user_nbr_of_reports[0]
                ]);
            }

        }
        else {
            $user_manager = new UserManager();
            $geek_sample = $user_manager->getRandomMembers();
            
            echo $this->twig->render('/front/homepage/disconnectedHome.twig',['geek_sample' => $geek_sample]);
        }

        
    }

    public function displayUserProfile() {
        if(isset($_SESSION['user'])) {
            $user_manager = new UserManager();
            $user = $user_manager->getMember($_SESSION['user']->id());
            $user_interests = $user->interests();
            $interests_array = explode(', ', $user_interests);
            $user->setInterests($interests_array);

            $interests_center = $user_manager->getInterests();

            echo $this->twig->render('front/userProfile.twig', [
                'user' => $user,
                'interests' => $interests_center
                ]);
   
        }
        else {
            $user_manager = new UserManager();
            $geek_sample = $user_manager->getRandomMembers();
            
            echo $this->twig->render('/front/homepage/disconnectedHome.twig',['geek_sample' => $geek_sample]);
        }
    }

    public function modifyProfile($id) {

        if(isset($_SESSION['user'])) {
            $user_manager = new UserManager();
            $user = $user_manager->getMember($id);

            if (!$user) {
                $data['status'] = 'error';
                $data['errors'] = ['user_not_found'];
                echo json_encode($data);
            }
            else {
                $profile_data = [];
                $errors = [];
                $profile_data['id'] = $id;

                /** Check if the specified gender is correct */
                $genders = ['Homme','Femme', 'Non-Binaire'];
                if(!empty($_POST['gender']) && !in_array($_POST['gender'], $genders)) {
                    $errors[] = 'gender_not_existing';
                }
                else {
                    $profile_data['gender'] = $_POST['gender'];
                }

                /**Check if specified birthday is correct */
                if(!empty($_POST['birthdate'])){
                    $birthdate = \DateTime::createFromFormat('j/m/Y', $_POST['birthdate']);
                    $birthdate->format('Y-m-d');

                    $actual_date = new \Datetime();
                    $datediff = $actual_date->diff($birthdate);
                    $age = $datediff->y;

                    if($age < 18) {
                        $errors[]='age_not_authorized';
                    } elseif ($age > 99){
                        $errors[]= 'too_old';
                    } else {
                        $profile_data['birthdate'] = $birthdate->format('Y-m-d');
                    }
                }
                
                /** Check if specified county is correct */
                if(!empty($_POST['county'])) {
                    $departments = json_decode(file_get_contents('https://geo.api.gouv.fr/departements'), true);
                    $departments_list = [];
                    foreach($departments as $department) {
                        $departments_list[] = $department['nom'];
                    }

                    if (!in_array($_POST['county'], $departments_list)) {
                        $errors[] = 'county_not_existing';
                    }
                    else{
                        $profile_data['county'] = $_POST['county'];
                    }
                }
                else {
                    $profile_data['county'] = $user->county();
                }

                /**Check if specified centers of interests are corrects */
                if(!empty($_POST['interests'])) {
                    $interests = $user_manager->getInterests();
                    $interests_id = [];
                    $profile_data['interests'] = [];

                    foreach($interests as $interest_entry) {
                        $interests_id[] = $interest_entry->id();
                    }
                    foreach($_POST['interests'] as $interest) {
                        if (!in_array($interest, $interests_id) || !$interest>0) {
                            $errors[] = 'interest_does_not_exists';
                        } else {
                            array_push($profile_data['interests'], (int) $interest);
                        }
                    }
                }
                

                /**Check if favorite quote is not just spaces */
                if(!empty($_POST['favorite-citation'])) {

                    if(preg_match('#^[[:blank:]\n]+$#', $_POST['favorite-citation'])) {
                        $errors[] = 'fav_quote_just_blanks';
                    }
                    elseif(strlen($_POST['favorite-citation']) > 255) {
                        $errors[] = 'fav_quote_too_long';
                    }
                    else {
                        $profile_data['favorite_citation'] =  $_POST['favorite-citation'];
                    }
                }

                /**Check if favorite character is not just spaces */
                if(!empty($_POST['identified-as'])) {

                    if(preg_match('#^[[:blank:]\n]+$#', $_POST['identified-as'])) {
                        $errors[] = 'fav_char_just_blanks';
                    }
                    elseif(strlen($_POST['identified-as']) > 50) {
                        $errors[] = 'fav_char_too_long';
                    }
                    else {
                        $profile_data['identified_as'] =  $_POST['identified-as'];
                    }
                }

                /**Check if profile picture is ok */
                if (!empty($_FILES['profile-picture']['name'])) {

                    $image_input_name = 'profile-picture';
                                
                    if($_FILES[$image_input_name]['error'] != 0) {
                        $errors[] = 'profile_picture_image_or_size_invalid';
                    }

                    if (empty($errors)) { 
                        $upload_result = $this->createImageInFolder($image_input_name, profile_picture_width, profile_picture_height, profile_picture_folder);

                        if($upload_result['status'] == 'success') {
                            $profile_data['profile_picture'] = $upload_result['image_name'];
                        }
                        else {
                            foreach ($upload_result['errors'] as $error){
                                $errors[] = 'profile_picture_' . $error;
                            }
                        }
                    }
                }
                else {
                    $profile_data['profile_picture'] = $user->profilePicture();
                }

                /**Check if profile banner is ok */
                if (!empty($_FILES['profile-banner']['name'])) {
                    $image_input_name = 'profile-banner';
                                
                    if($_FILES[$image_input_name]['error'] != 0) {
                        $errors[] = 'profile_banner_image_or_size_invalid';
                    }

                    if (empty($errors)) { 
                        $upload_result = $this->createImageInFolder($image_input_name, profile_banner_width, profile_banner_height, profile_banner_folder);

                        if($upload_result['status'] == 'success') {
                            $profile_data['profile_banner'] = $upload_result['image_name'];
                        }
                        else {
                            foreach ($upload_result['errors'] as $error){
                                $errors[] = 'banner_picture_' . $error;
                            }
                        }
                    }
                }
                else {
                    $profile_data['profile_banner'] = $_SESSION['user']->profileBanner();
                }

                /**Check if description is correct*/
                if(!empty($_POST['description'])) {

                    if(preg_match('#^[[:blank:]\n]+$#', $_POST['description'])) {
                        $errors[] = 'description_just_blanks';
                    }
                    elseif(strip_tags(strlen($_POST['description'])) > 4000) {
                        $errors[] = 'description_too_long';
                        
                    }
                    else {
                        $profile_data['description'] =  $_POST['description'];
                    }
                }
                else {
                    $profile_data['description'] = $user->description();
                }

                if (!empty($errors)) {
                    $data['status'] = 'error';
                    $data['errors'] = $errors;
                    echo json_encode($data);
                }
                else {
                    $modification_worked = $user_manager->modifyProfile($profile_data);

                    if(!$modification_worked){
                        $data['status'] = 'error';
                        $data['errors'] = ['modification_server_didnt_worked'];
                        echo json_encode($data);
                    } 
                    else  {
                        $_SESSION['user']->setGender($profile_data['gender']);
                        $_SESSION['user']->setBirthdate($profile_data['birthdate']);
                        $_SESSION['user']->setCounty($profile_data['county']);
                        $_SESSION['user']->setFavoriteCitation($profile_data['favorite_citation']);
                        $_SESSION['user']->setIdentifiedAs($profile_data['identified_as']);
                        $_SESSION['user']->setProfilePicture($profile_data['profile_picture']);
                        $_SESSION['user']->setProfileBanner($profile_data['profile_banner']);
                        $_SESSION['user']->setDescription($profile_data['description']);

                        $client = new Client(new Ruleset());
                        $emoji_description = $client->toImage($profile_data['description']);
                        $data['description'] = $emoji_description;
                        $data['status'] = 'success';
                        echo json_encode($data);
                    }
                }
            }
        
        }
        else {
            $user_manager = new UserManager();
            $geek_sample = $user_manager->getRandomMembers();
            
            echo $this->twig->render('/front/homepage/disconnectedHome.twig',['geek_sample' => $geek_sample]);
        }
    }

    public function displayAccount(){
        if(isset($_SESSION['user'])) {
            $user_manager = new UserManager();
            $user = $user_manager->getMember($_SESSION['user']->id());

            $interests_center = $user_manager->getInterests();

            echo $this->twig->render('front/userAccount.twig', [
                'user' => $user
            ]);
   
        }
        else {
            $user_manager = new UserManager();
            $geek_sample = $user_manager->getRandomMembers();
            
            echo $this->twig->render('/front/homepage/disconnectedHome.twig',['geek_sample' => $geek_sample]);
        }
    }

    public function modifyAccount($id){
        if (isset($_SESSION['user'])) {
            $user_manager = new UserManager();
            $user = $user_manager->getMember($id);

            if (!$user) {
                $data['status'] = 'error';
                $data['errors'] = ['user_not_found'];
                echo json_encode($data);
            }
            else {
                $account_data = [];
                $errors = [];
                $account_data['id'] = $id;

                /** Check if the pseudo is correct */
                if (!empty($_POST['username'])) {
                    $members = $user_manager->getMembers();

                    foreach($members as $member) {
                        if ($member->username() === $_POST['username'] && $member->username() != $user->username()) {
                            $errors[] = 'username_already_used';
                        }   
                    }

                    if (!preg_match('#[0-9A-Za-záàâäãåçéèêëíìîïñóòôöõúùûüýÿæœÁÀÂÄÃÅÇÉÈÊËÍÌÎÏÑÓÒÔÖÕÚÙÛÜÝŸÆŒ._-]{6,}#', $_POST['username'])){
                        $errors[] = 'username_not_matching_regex' ;
                    }

                    if (strlen($_POST['username']) > 20) {
                        $errors[] = 'username_too_long';
                    }

                    if(!in_array('username_already_used', $errors) || !in_array('username_not_matching_regex', $errors) || !in_array('username_too_long', $errors)) {
                        $account_data['username'] = $_POST['username'];
                    }
                }
                else {
                    $account_data['username'] = $user->username();
                }

                /** Check if the password is correct */
                if(!empty($_POST['new-password'])) {
                    if ($_POST['new-password'] != $_POST['password-confirmation']) {
                        $errors[] = 'passwords_not_identical' ;
                    }
        
                    if (!preg_match('#^((?=\S*?[A-Z])(?=\S*?[a-z])(?=\S*?[0-9]).{7,})\S$#', $_POST['new-password'])) {
                        $errors[] = 'password_not_matching_regex';
                    }

                    if(!in_array('passwords_not_identical', $errors) || !in_array('password_not_matching_regex', $errors)) {
                        $account_data['password'] = password_hash($_POST['new-password'], PASSWORD_DEFAULT);
                    }
                }
                else {
                    $account_data['password'] = $user->password();
                }

                /** Check if the email is correct */
                if(!empty($_POST['email'])) {
                    $members = $user_manager->getMembers();

                    foreach($members as $member) {
                        if ($member->email() === $_POST['email'] && $member->email() != $user->email()) {
                            $errors[] = 'email_already_used';
                        }
                    }

                    if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
                        $errors[] = 'email_invalid';
                    }

                    if(!in_array('email_already_used', $errors) || !in_array('email_invalid', $errors)) {
                        $account_data['email'] = $_POST['email'];
                    }
                }
                else {
                    $account_data['email'] = $user->email();
                }
            }

            if (!empty($errors)) {
                $data['status'] = 'error';
                $data['errors'] = $errors;
                echo json_encode($data);
            }
            else {
                $modification_worked = $user_manager->modifyAccount($account_data);

                if(!$modification_worked){
                    $data['status'] = 'error';
                    $data['errors'] = ['modification_server_didnt_worked'];
                    echo json_encode($data);
                } 
                else  {
                    $_SESSION['user']->setUsername($account_data['username']);
                    $_SESSION['user']->setEmail($account_data['email']);
                    $_SESSION['user']->setPassword(password_hash($account_data['password'], PASSWORD_DEFAULT));
                    $data['username'] = $account_data['username'];
                    $data['status'] = 'success';
                    echo json_encode($data);
                }
            }
        }
        else {
            $user_manager = new UserManager();
            $geek_sample = $user_manager->getRandomMembers();
            
            echo $this->twig->render('/front/homepage/disconnectedHome.twig',['geek_sample' => $geek_sample]);
        }
    }

}
