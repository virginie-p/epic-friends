<?php
namespace App\Entity;

class Interest extends Entity {
    protected   $id,
                $interest_name, 
                $creation_date;
    
    /*GETTERS*/
    public function id() {
        return $this->id;
    }

    public function interestName() {
        return $this->interest_name;
    }

    public function creationDate() {
        return $this->creation_date;
    }

    /*SETTERS*/

    public function setId(int $id) {
        $this->id = $id;
    }

    public function setInterestName($interest_name) {
        $this->interest_name = $interest_name;
    }

    public function setCreationDate($creation_date) {
        $this->creation_date = $creation_date;
    }
}