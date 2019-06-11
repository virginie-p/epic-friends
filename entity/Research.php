<?php
namespace App\Entity;

class Research extends Entity {
    protected   $gender,
                $county,
                $interests = [],
                $age_range;

    /**GETTERS */

    public function gender() {
        return $this->gender;
    }

    public function county() {
        return $this->county;
    }

    public function interests() {
        return $this->county;
    }

    public function age() {
        return $this->age;
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

    public function setAgeRange($age_range) {
        $this->age = $age;
    }
}

