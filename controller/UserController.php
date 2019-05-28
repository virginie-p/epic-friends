<?php

namespace App\Controller;
use App\Entity\User;
use App\Model\UserManager;

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

            if (!preg_match('#[0-9A-Za-záàâäãåçéèêëíìîïñóòôöõúùûüýÿæœÁÀÂÄÃÅÇÉÈÊËÍÌÎÏÑÓÒÔÖÕÚÙÛÜÝŸÆŒ._-]{6,}#', $_POST['subscribe-username'])){
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
            $birthdate->format('Y-m-d');

            $actual_date = new \Datetime();
            $datediff = $actual_date->diff($birthdate);
            $age = $datediff->y;

            if($age < 18) {
                $errors[]='age_not_authorized';
            }
            
            $user_data = array(
                'user_type' => 5,
                'username' => $_POST['subscribe-username'],
                'password' => password_hash($_POST['subscribe-password'], PASSWORD_DEFAULT),
                'email' => $_POST['email'],
                'birthdate' => $birthdate->format('Y-m-d')
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
        $user_manager = new UserManager();
        if ($_SESSION['user']->county() == NULL){
            $new_users = $user_manager->getNewMembers($_SESSION['user']);
        }
        else {
        $new_users = $user_manager->getNewCountyMembers($_SESSION['user']);
        }
        
        return $new_users;
    }

    public function displayProfile($id) {
        if(isset($_SESSION['user'])) {
            $user_manager = new UserManager();
            $member = $user_manager->getMember($id);

            echo $this->twig->render('front/memberProfile.twig', ['member' => $member]);
        }
        else {
            echo $this->twig->render('/front/homepage/disconnectedHome.twig');
        }

        
    }

}
