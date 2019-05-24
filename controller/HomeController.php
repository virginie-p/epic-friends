<?php
namespace App\Controller;

class HomeController extends Controller {

    public function displayDisconnectedHome() {
        // $testimony_manager = new TestimonyManager();
        // $testimony_manager->getTop3Comments();

        echo $this->twig->render('/front/homepage/disconnectedHome.twig');
    }

    public function createUser() {
        $user_data = [];
        $errors = [];

        if (!empty($_POST['subscribe-username']) && !empty($_POST['subscribe-password']) && !empty($_POST['password-confirmation']) 
            && !empty($_POST['email']) && !empty($_POST['lastname']) && !empty($_POST['firstname']) && !empty($_FILES['profile-picture']['name'])) {
            $user_manager = new UserManager();
            $users = $user_manager->getMembers();

            foreach($users as $user) {
                if ($user->username() == $_POST['subscribe-username']) {
                    $errors[] = 'username_already_used';
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

            if(preg_match('#^[[:blank:]\n]+$#', $_POST['lastname']) || preg_match('#^[[:blank:]\n]+$#', $_POST['firstname'])) {
                $errors[] = 'just_spaces';
            }

            $user_data = array(
                'user_type' => 3,
                'username' => $_POST['subscribe-username'],
                'password' => password_hash($_POST['subscribe-password'], PASSWORD_DEFAULT),
                'email' => $_POST['email'],
                'lastname' => htmlspecialchars($_POST['lastname']),
                'firstname' => htmlspecialchars($_POST['firstname'])
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
                $affected_lines = $user_manager->createUser($new_user);

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
}