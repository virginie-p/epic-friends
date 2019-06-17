<?php

namespace App\Entity;
use Emojione\Client;
use Emojione\Ruleset;


class User extends Entity implements \JsonSerializable {
    protected   $id,
                $user_type_id,
                $username,
                $password,
                $email,
                $birthdate,
                $gender,
                $county,
                $favorite_citation,
                $interests = [],
                $description,
                $profile_picture,
                $profile_banner,
                $identified_as,
                $number_of_reports,
                $creation_date;

    public function jsonSerialize() {
        return [
            'id' => $this->id,
            'username' => $this->username,
            'age' => $this->getAge(),
            'interests' => $this->interests,
            'profile_picture' => $this->profile_picture
        ];
    }
          
    /** GETTERS */
    public function id() {
        return $this->id;
    }

    public function userTypeId() {
        return $this->user_type_id;
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

    public function interests() {
        return $this->interests;
    }

    public function description() {
        $client = new Client(new Ruleset());
        $emoji_description = $client->toImage($this->description);
        return $emoji_description;
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

    public function numberOfReports() {
        return $this->number_of_reports;
    }

    public function creationDate() {
        return $this->creation_date;
    }

    /** SETTERS */
    public function setId($id) {
        $this->id = (int) $id;
    }

    public function setUserTypeId($user_type_id) {
        $this->user_type_id = $user_type_id;
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

    public function setInterests(array $interests) {
        $this->interests = $interests;
    }

    public function setDescription($description) {
        $this->description = $description;
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

    public function setNumberOfReports(int $number_of_reports) {
        $this->number_of_reports = $number_of_reports;
    }

    public function setCreationDate($creation_date) {
        $this->creation_date = $creation_date;
    }

    /**METHODS */

    public function getAge() {
        $birthdate = new \Datetime($this->birthdate);
        $birthdate->format('Y-m-d');

        $actual_date = new \Datetime();
        $datediff = $actual_date->diff($birthdate);
        $age = $datediff->y;

        return $age;
    }
}