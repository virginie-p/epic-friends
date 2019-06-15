<?php

namespace App\Controller;
use App\Model\MailboxManager;
use App\Model\UserManager;
use App\Entity\Mail;


class MailboxController extends Controller {

    public function displayMailbox() {
        if(isset($_SESSION['user'])) {
            $mailbox_manager = new MailboxManager(); 
            $users_contacted = $mailbox_manager->getUsersContacted($_SESSION['user']->id());

            if(!empty($users_contacted)){
                $mailbox_manager->openLastUserContactedMessages($users_contacted[0]->id(), $_SESSION['user']->id());
                $unread_messages = $mailbox_manager->getUnreadMessages($_SESSION['user']->id());
            
                $last_user_contacted_messages = $mailbox_manager->getLastUserContactedMessages($users_contacted[0]->id(), $_SESSION['user']->id());
                echo $this->twig->render('/front/mailbox.twig', [
                    'users_contacted' => $users_contacted,
                    'unread_messages' => $unread_messages,
                    'last_user_contacted_id' => $users_contacted[0]->id(),
                    'last_user_contacted_messages' => $last_user_contacted_messages
                ]);
            }
            else {
                echo $this->twig->render('/front/empty-mailbox.twig');
            }
            
        }
        else {
            echo $this->twig->render('/front/homepage/disconnectedHome.twig');
        }
    }

    public function sendMessage($id) {
        if(isset($_SESSION['user'])) {
            $user_manager = new UserManager();
            $user = $user_manager->getMember($id);

            if (!$user) {
                $data['status'] = 'error';
                $data['errors'] = ['user_not_found'];
                echo json_encode($data);
            }
            else {
                $message_data = [];
                $errors = [];
                $message_data['recipient_id'] = $id;
                $message_data['sender_id'] = $_SESSION['user']->id();

                /**Check if message is correct */
                if(!empty($_POST['message'])) {

                    if(preg_match('#^[[:blank:]\n]+$#', $_POST['message'])) {
                        $errors[] = 'message_just_blanks';
                    }

                    if(strip_tags(strlen($_POST['message'])) > 2000) {
                        $errors[] = 'message_too_long';
                        
                    }

                    if (!in_array('message_juste_blanks', $errors) || !in_array('message_too_long', $errors)) {
                        $message_data['message'] =  $_POST['message'];
                    }
                }
                else {
                    $errors[] = 'message_not_filled';
                }

                if (!empty($errors)) {
                    $data['status'] = 'error';
                    $data['errors'] = $errors;
                    echo json_encode($data);
                }
                else {
                    $mail = new Mail($message_data);

                    $mailbox_manager = new MailboxManager();
                    $message_sent = $mailbox_manager->sendMessage($mail);

                    if(!$message_sent){
                        $data['status'] = 'error';
                        $data['errors'] = ['message_expedition_didnt_worked'];
                        echo json_encode($data);
                    } 
                    else {
                        $data['status'] = 'success';
                        echo json_encode($data);
                    }
                }


            }

            $mailbox_manager = new MailboxManager();


        }
        else {
            echo $this->twig->render('/front/homepage/disconnectedHome.twig');
        }

    }

    public function getUserNewMessages($member_id, $last_message_id) {
        if(isset($_SESSION['user'])) {
            $mailbox_manager = new MailboxManager();
            $new_messages = $mailbox_manager->getUserNewMessages($member_id, $_SESSION['user']->id(), $last_message_id);
            if (!empty($new_messages)){
                echo json_encode([
                    'status' => 'success',
                    'new_messages' => $new_messages, 
                    'user_id' => $_SESSION['user']->id()
                ]);
            }
        }
        else {
            echo $this->twig->render('/front/homepage/disconnectedHome.twig');
        }
    }
}