<?php 

namespace App\Entity;
use Emojione\Client;
use Emojione\Ruleset;

class Mail extends Entity implements \JsonSerializable {
    protected $id,
              $sender_id,
              $sender_profile_picture,
              $sender_username,
              $recipient_id,
              $message = NULL,
              $opened_by_recipient,
              $creation_date; 

    public function jsonSerialize() {
        return [
            'id' => $this->id,
            'sender_id' => $this->sender_id,
            'sender_profile_picture' => $this->sender_profile_picture,
            'sender_username' => $this->sender_username,
            'recipient_id' => $this->recipient_id,
            'message' => $this->message(),
            'opened_by_recipient' => $this->opened_by_recipient,
            'creation_date' => $this->creation_date
        ];
    }

    /**GETTERS */

    public function id() {
        return $this->id;
    }

    public function senderId() {
        return $this->sender_id;
    }

    public function senderProfilePicture() {
        return $this->sender_profile_picture;
    }

    public function senderUsername() {
        return $this->sender_username;
    }

    public function recipientId() {
        return $this->recipient_id;
    }

    public function message() {
        $client = new Client(new Ruleset());
        $emoji_message = $client->toImage($this->message);
        return $emoji_message;
    }

    public function openedByRecipient() {
        return $this->opened_by_recipient;
    }

    public function creationDate() {
        $date = new \DateTime($this->creation_date);
        $creation_date = $date->format('d-m-Y H:i');
        return $creation_date ;
    }

    /**SETTERS */

    public function setId($id) {
        $this->id = $id;
    }

    public function setSenderId($sender_id) {
        $this->sender_id = $sender_id;
    }

    public function setRecipientId($recipient_id) {
        $this->recipient_id = $recipient_id;
    }

    public function setMessage($message) {
        $this->message = $message;
    }

    public function setOpenedByRecipient($opened_by_recipient) {
        $this->opened_by_recipient = $opened_by_recipient;
    }

    public function setCreationDate($creation_date) {
        $this->creation_date = $creation_date;
    }
}