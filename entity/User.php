<?php

namespace App\Entity;


class User extends Entity {
    protected   $id,
                $user_type,
                $username,
                $password,
                $email,
                $firstname,
                $lastname,
                $birthdate,
                $gender,
                $county,
                $favorite_citation,
                $description,
                $profile_picture,
                $profile_banner,
                $identified_as,
                $creation_date;
          
    /** GETTERS */
    public function id() {
        return $this->id;
    }

    public function userType() {
        return $this->user_type;
    }

    public function username() {
        return $this->username;     
    }

    public function password() {
        return $this->password;
    }

    public function email() {
        return $this->email;
    }

    public function firstname()  {
        return $this->firstname;
    }

    public function lastname() {
        return $this->lastname;
    }

    public function birthdate() {
        return $this->birthdate;
    }

    public function gender() {
        return $this->gender;
    }

    public function county() {
        return $this->county;
    }

    public function favoriteCitation() {
        return $this->favorite_citation;
    }

    public function description() {
        return $this->description;
    }

    public function profilePicture() {
        return $this->profile_picture;
    }

    public function profileBanner() {
        return $this->profile_banner;
    }
    
    public function identifiedAs() {
        return $this->identified_as;
    }

    public function creationDate() {
        return $this->creation_date;
    }

    /** SETTERS */
    public function setId($id) {
        $this->id = (int) $id;
    }

    public function setUserType($user_type) {
        $this->user_type = $user_type;
    }

    public function setUsername($username) {
        $this->username = $username;
    }
    
    public function setPassword($password) {
        $this->password = $password;
    }

    public function setEmail($email) {
        $this->email = $email;
    }

    public function setFirstname($firstname){
        $this->firstname = $firstname;
    }

    public function setLastname($lastname) {
        $this->lastname = $lastname;
    }

    public function setBirthdate($birthdate) {
        $this->birthdate = $birthdate;
    }

    public function setGender($gender) {
        $this->gender = $gender;
    }

    public function setCounty($county) {
        $this->county = $county;
    }

    public function setFavoriteCitation($favorite_citation) {
        $this->favorite_citation = $favorite_citation;
    }

    public function setDescription($description) {
        $this->setDescription = $description;
    }

    public function setProfilePicture($profile_picture) {
        $this->profile_picture = $profile_picture;
    }

    public function setProfileBanner($profile_banner) {
        $this->profile_banner = $profile_banner;
    }

    public function setIdentifiedAs($identified_as) {
        $this->identified_as = $identified_as;
    }

    public function setCreationDate($creation_date) {
        $this->creation_date = $creation_date;
    }
}