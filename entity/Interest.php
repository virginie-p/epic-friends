<?php
namespace App\Entity;

class Interest extends Entity {
    protected   $id,
                $interest_name;
    
    /*GETTERS*/
    public function id() {
        return $this->id;
    }

    public function interestName() {
        return $this->interest_name;
    }

    /*SETTERS*/

    public function setId($id) {
        $this->id = (int) $id;
    }

    public function setInterestName($interest_name) {
        $this->interest_name = $interest_name;
    }
}