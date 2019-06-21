<?php

namespace App\Model;
use App\Entity\Mail;
use App\Entity\User;
use \PDO;

class MailboxManager extends Manager {

    public function getUsersContacted($user_id) {
        $db = $this->MySQLConnect();
        $req = $db->prepare('SELECT member_id AS id, username, profile_picture, MAX(creation_date) AS creation_date
                            FROM (SELECT sender_id AS member_id, username, profile_picture, messages.creation_date
                                  FROM project_5_messages AS messages
                                  INNER JOIN project_5_users_parameters AS users_parameters ON users_parameters.id = messages.sender_id
                                  INNER JOIN project_5_users_profiles AS users_profiles ON users_parameters.id = users_profiles.user_id
                                  WHERE recipient_id = :user_id
                                  UNION ALL
                                  SELECT recipient_id AS member_id, username, profile_picture, messages.creation_date
                                  FROM project_5_messages AS messages
                                  INNER JOIN project_5_users_parameters as users_parameters ON users_parameters.id = messages.recipient_id
                                  INNER JOIN project_5_users_profiles AS users_profiles ON users_parameters.id = users_profiles.user_id
                                  WHERE sender_id = :user_id)
                            AS members_contacted
                            GROUP BY id
                            ORDER BY creation_date DESC');

        $req->execute(['user_id' => $user_id]);
        $req->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'App\Entity\User');
        $result = $req->fetchAll();
        // $req->debugDumpParams();
        // die();
        
        return $result;
    }

    public function getUnreadMessages($user_id) {
        $db = $this->MySQLConnect();

        $req = $db->prepare('SELECT sender_id,
                                    messages.id AS id,
                                    recipient_id, 
                                    users_parameters.username AS sender_username,
                                    users_profiles.profile_picture AS sender_profile_picture,
                                    messages.creation_date
                            FROM project_5_messages AS messages
                            INNER JOIN project_5_users_parameters AS users_parameters ON messages.sender_id = users_parameters.id
                            INNER JOIN project_5_users_profiles AS users_profiles ON messages.sender_id = users_profiles.user_id
                            WHERE recipient_id = :user_id AND opened_by_recipient = 0 AND messages.creation_date = (SELECT MAX(messages2.creation_date)
                                                                                                                    FROM project_5_messages AS messages2
                                                                                                                    WHERE messages2.sender_id = messages.sender_id)
                            ORDER BY messages.id DESC');
        $req->execute(['user_id' => $user_id]);
        $req->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'App\Entity\Mail');
        $result = $req->fetchAll();
        return $result;
    }

    public function getUserContactedMessages($contacted_user_id, $user_id) {
        $db = $this->MySQLConnect();
        $req = $db->prepare('SELECT * FROM project_5_messages 
                            WHERE (sender_id = :contacted_user_id AND recipient_id = :user_id) OR (sender_id = :user_id AND recipient_id = :contacted_user_id) 
                            ORDER BY creation_date ASC');
        $req->execute([
            'contacted_user_id'=> $contacted_user_id,
            'user_id' => $user_id
        ]);

        $req->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'App\Entity\Mail');
        $result = $req->fetchAll();
        // $req->debugDumpParams();
        // die();

        return $result;
    }

    public function openUserContactedMessages($contacted_user_id, $user_id) {
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
        // $req->debugDumpParams();
        // die();

        return $result;
    }

    public function getUserNewMessages($contacted_user_id, $user_id, $last_message_id) {
        $db = $this->MySQLConnect();
        $req = $db->prepare('SELECT * FROM project_5_messages 
                            WHERE ((sender_id = :contacted_user_id AND recipient_id = :user_id) OR (sender_id = :user_id AND recipient_id = :contacted_user_id))
                                   AND id > :last_message_id
                            ORDER BY creation_date ASC');
        $req->execute([
            'last_message_id'=> $last_message_id,
            'contacted_user_id' => $contacted_user_id,
            'user_id' => $user_id,
        ]);

        $req->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'App\Entity\Mail');
        $result = $req->fetchAll();

        return $result;

    }
}