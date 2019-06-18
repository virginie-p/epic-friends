<?php
namespace App\Controller;
use App\Model\UserManager;
use App\Model\SearchManager;
use App\Entity\Research;

class ResearchController extends Controller {

    public function displaySearchEngine() {
        $user_manager = new UserManager();
        $interests_center = $user_manager->getInterests();
        echo $this->twig->render('/front/searchEngine.twig', ['interests' => $interests_center]);
    }

    public function displaySearchResults() {
        if(isset($_SESSION['user'])) {
            $search_data = [];
            $errors = [];

            /** Check if the specified gender is correct */
            
            if(!empty($_POST['gender'])) {
                $genders = ['Homme','Femme', 'Non-Binaire'];
                if(!in_array($_POST['gender'], $genders)) {
                    $errors[] = 'gender_not_existing';
                } 
                else {
                    $search_data['gender'] = $_POST['gender'];
                }
            }


            /**Check if the specified county is correct */
            if(!empty($_POST['county'])) {
                $departments = json_decode(file_get_contents('https://geo.api.gouv.fr/departements'), true);
                $departments_list = [];
                foreach($departments as $department) {
                    $departments_list[] = $department['nom'];
                }

                if (!in_array($_POST['county'], $departments_list)) {
                    $errors[] = 'county_not_existing';
                }
                else{
                    $search_data['county'] = $_POST['county'];
                }
            }
            
            /**Check if age range is correct (between 18 and 99)*/
            if(!empty($_POST['age_range'])){
                $age_range = explode(';', $_POST['age_range']);
                $dates = [];
                
                if ($age_range[0] < 18 || $age_range[0] > 99) {
                    $errors[] = 'min_age_not_matching';
                }
                else {
                    $current_date = new \DateTime();
                    $start_birthday = $current_date->sub(new \DateInterval('P'. $age_range[0] .'Y'));
                    $min_birthday = $start_birthday->format('Y-m-d'); 
                }

                if ($age_range[1] < 18 || $age_range[1] > 99) {
                    $errors[] = 'max_age_not_matching';
                }
                else {
                    $current_date = new \DateTime();
                    $end_birthday = $current_date->sub(new \DateInterval('P'. $age_range[1] .'Y'));
                    $max_birthday = $end_birthday->format('Y-m-d');
                }

                if (!in_array('min_age_not_matching', $errors) || !in_array('max_age_not_matching', $errors)) {
                    $search_data['min_birthday'] = $min_birthday;
                    $search_data['max_birthday'] = $max_birthday;
                }
            }

            /**Check if specified centers of interests are corrects */
            if(isset($_POST['interests'])) {
                $user_manager = new UserManager();
                $interests = $user_manager->getInterests();
                $interests_id = [];
                $search_data['interests'] = [];

                foreach($interests as $interest_entry) {
                    $interests_id[] = $interest_entry->id();
                }
                foreach($_POST['interests'] as $interest) {
                    if (!in_array($interest, $interests_id) || !$interest>0) {
                        $errors[] = 'interest_does_not_exists';
                    } else {
                        array_push($search_data['interests'], (int) $interest);
                    }
                }
            }

            if (!empty($errors)) {
                $data['status'] = 'error';
                $data['errors'] = $errors;
                echo json_encode($data);
            }
            else {
                $start_from_user = $_POST['offset'];
                $number_of_user = 2;
                $research = new Research($search_data);
                $connected_user_id = $_SESSION['user']->id();
                $search_manager = new SearchManager();
                $search_worked = $search_manager->searchMembers($research, $start_from_user, $number_of_user, $connected_user_id);
                $results_number = $search_manager->countSearchedMembers($research, $connected_user_id);
                if(!$search_worked || !$results_number){
                    $data['status'] = 'error';
                    $data['errors'] = ['research_do_not_return_anything'];
                    echo json_encode($data);
                } 
                else  {
                    $data['status'] = 'success';
                    $data['research'] = $research;
                    $data['results_number'] = $results_number;
                    $data['results'] = $search_worked;
                    echo json_encode($data);
                }
            }

        }
        else {
            $user_manager = new UserManager();
            $geek_sample = $user_manager->getRandomMembers();
            
            echo $this->twig->render('/front/homepage/disconnectedHome.twig',['geek_sample' => $geek_sample]);
        }
    }
}
