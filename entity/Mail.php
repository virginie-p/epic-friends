<?php 

namespace App\Entity;
use Emojione\Client;
use Emojione\Ruleset;

class Mail extends Entity {
    protected $sender_id,
              $recipient_id,
              $message = NULL,
              $opened_by_recipient,
              $creation_date; 

    /**GETTERS */

    public function senderId() {
        return $this->sender_id;
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