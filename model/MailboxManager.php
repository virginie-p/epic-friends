<?php

namespace App\Model;
use App\Entity\Mail;
use App\Entity\User;
use \PDO;

class MailboxManager extends Manager {

    public function getUsersContacted($user_id) {
        $db = $this->MySQLConnect();
        $req = $db->prepare('SELECT DISTINCT username,
                                    profile_picture,
                                    member_id AS id            
                            FROM (SELECT sender_id as member_id, creation_date
                                  FROM project_5_messages
                                  WHERE recipient_id = :user_id
                                  UNION
                                  SELECT recipient_id as member_id, creation_date
                                  FROM project_5_messages
                                  WHERE sender_id = :user_id) 
                            AS members_contacted 
                            INNER JOIN project_5_users_parameters as users_parameters ON users_parameters.id = member_id
                            INNER JOIN project_5_users_profiles AS users_profiles ON users_parameters.id = users_profiles.user_id
                            ORDER BY members_contacted.creation_date DESC
                            ');

        $req->execute(['user_id' => $user_id]);
        $req->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'App\Entity\User');
        $result = $req->fetchAll();
        // $req->debugDumpParams();
        // die();
        
        return $result;
    }

    public function getUnreadMessages($user_id) {
        $db = $this->MySQLConnect();
        $req = $db->prepare('SELECT sender_id
                            FROM project_5_messages
                            WHERE recipient_id = :user_id AND opened_by_recipient = 0');
        $req->execute(['user_id' => $user_id]);
        $req->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'App\Entity\Mail');
        $result = $req->fetchAll();

        return $result;
    }

    public function getLastUserContactedMessages($contacted_user_id, $user_id) {
        $db = $this->MySQLConnect();
        $req = $db->prepare('SELECT * FROM project_5_messages WHERE (sender_id = :contacted_user_id AND recipient_id = :user_id) OR (sender_id = :user_id AND recipient_id = :contacted_user_id) ORDER BY creation_date ASC');
        $req->execute([
            'contacted_user_id'=> $contacted_user_id,
            'user_id' => $user_id
        ]);

        $req->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'App\Entity\Mail');
        $result = $req->fetchAll();
        // var_dump($result);
        // die;

        return $result;
    }

    public function openLastUserContactedMessages($contacted_user_id, $user_id) {
        $db = $this->MySQLConnect();
        $req = $db->prepare('UPDATE project_5_messages SET opened_by_recipient = 1 WHERE recipient_id = :user_id AND sender_id = :contacted_user_id');
        $result = $req->execute([
            'contacted_user_id'=> $contacted_user_id,
            'user_id' => $user_id
        ]);
        return $result;
    }

    public function sendMessage(Mail $mail) {
        $db = $this->MySQLConnect();
        $req = $db->prepare('INSERT INTO project_5_messages(sender_id,
                                                            recipient_id,
                                                            message)
                            VALUES (:sender_id, :recipient_id, :message)');
        $result = $req->execute([
            'sender_id' => $mail->senderId(),
            'recipient_id'=> $mail->recipientId(),
            'message' => $mail->message()
        ]);

        return $result;
    }

    public function getUserNewMessages($member_id, $user_id, $last_message_id) {
        $db = $this->MySQLConnect();
        $req = $db->prepare('SELECT * FROM project_5_messages 
                            WHERE ((sender_id = :member_id AND recipient_id = :user_id) 
                                   OR (sender_id = :user_id AND recipient_id = :member_id))
                                   AND id > :last_message_id
                            ORDER BY creation_date ASC');
        $req->execute([
            'last_message_id'=> $last_message_id,
            'member_id' => $member_id,
            'user_id' => $user_id,
        ]);

        $req->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'App\Entity\Mail');
        $result = $req->fetchAll();

        return $result;

    }
}