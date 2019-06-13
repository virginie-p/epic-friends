<?php
namespace App\Entity;

class Research extends Entity {
    protected   $gender,
                $county,
                $interests = [],
                $max_birthday,
                $min_birthday;

    /**GETTERS */

    public function gender() {
        return $this->gender;
    }

    public function county() {
        return $this->county;
    }

    public function interests() {
        return $this->interests;
    }

    public function maxBirthday() {
        return $this->max_birthday;
    }
    public function minBirthday() {
        return $this->min_birthday;
    }

    /**SETTERS */

    public function setGender($gender){
        $this->gender = $gender;
    }

    public function setCounty($county){
        $this->county = $county;
    }

    public function setInterests(array $interests){
        $this->interests = $interests;
    }

    public function setMaxBirthday($max_birthday) {
        $this->max_birthday = $max_birthday;
    }

    public function setMinBirthday($min_birthday) {
        $this->min_birthday = $min_birthday;
    }
}

