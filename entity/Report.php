<?php
namespace App\Entity;

class Report extends Entity {
    protected $id,
              $reported_user_id,
              $informer_user_id,
              $report_reason,
              $creation_date;

    /**GETTERS */
    public function id() {
        return $this->id;
    }
    
    public function reportedUserId() {
        return $this->reported_user_id;
    }

    public function informerUserId() {
        return $this->informer_user_id;
    }

    public function reportReason() {
        return $this->report_reason;
    }

    public function creationDate() {
        return $this->creation_date;
    }

    /**SETTERS */

    public function setId($id) {
        $this->id = $id;
    }

    public function setReportedUserId($reported_user_id) {
        $this->reported_user_id = $reported_user_id;
    }

    public function setInformerUserId($informer_user_id) {
        $this->informer_user_id = $informer_user_id;
    }

    public function setReportReason($report_reason) {
        $this->report_reason = $report_reason;
    }

    public function setCreationDate($creation_date) {
        $this->creation_date = $creation_date;
    }

}