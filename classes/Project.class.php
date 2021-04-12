<?php


class Project {
    private $projectID;
    private $projectName;
    private $projectLeader;
    private $startTime;
    private $finishTime;
    private $status;
    private $customer;
    private $activePhase;
    private bool $isAcceptedByAdmin;

    public function __construct() {
    }

    public function getProjectID(){return $this->projectID;}
    public function setProjectID($projectID): void{$this->projectID = $projectID;}
    public function getProjectName(){return $this->projectName;}
    public function setProjectName($projectName): void{$this->projectName = $projectName;}
    public function getProjectLeader(){return $this->projectLeader;}
    public function setProjectLeader($projectLeader): void{$this->projectLeader = $projectLeader;}
    public function getStartTime(){return $this->startTime;}
    public function setStartTime($startTime): void{$this->startTime = $startTime;}
    public function getFinishTime(){return $this->finishTime;}
    public function setFinishTime($finishTime): void{$this->finishTime = $finishTime;}
    public function getStatus(){return $this->status;}
    public function setStatus($status): void{$this->status = $status;}
    public function getCustomer(){return $this->customer;}
    public function setCustomer($customer): void{$this->customer = $customer;}
    public function getActivePhase(){return $this->activePhase;}
    public function setActivePhase($activePhase): void{$this->activePhase = $activePhase;}
    public function isAcceptedByAdmin(): bool{return $this->isAcceptedByAdmin;}
    public function setIsAcceptedByAdmin(bool $isAcceptedByAdmin): void{$this->isAcceptedByAdmin = $isAcceptedByAdmin;}
}