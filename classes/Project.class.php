<?php

class Project {
    private $projectName;
    private $projectLeader;
    private $startTime;
    private $finishTime;
    private $status;
    private $customer;
    private $isAcceptedByAdmin;

    public function __construct() {
    }

    //Getters
    public function getProjectName(): mixed { return $this->projectName; }
    public function getProjectLeader(): mixed { return $this->projectLeader; }
    public function getStartTime(): mixed { return $this->startTime; }
    public function getFinishTime(): mixed { return $this->finishTime; }
    public function getStatus(): mixed { return $this->status; }
    public function getCustomer(): mixed { return $this->customer; }
    public function getIsAcceptedByAdmin() { return $this->isAcceptedByAdmin; }

    //Setters
    public function setProjectName(mixed $projectName): void { $this->projectName = $projectName; }
    public function setProjectLeader(mixed $projectLeader): void { $this->projectLeader = $projectLeader; }
    public function setStartTime(mixed $startTime): void { $this->startTime = $startTime; }
    public function setFinishTime(mixed $finishTime): void { $this->finishTime = $finishTime; }
    public function setStatus(mixed $status): void { $this->status = $status; }
    public function setCustomer(mixed $customer): void { $this->customer = $customer; }
    public function setIsAcceptedByAdmin($isAcceptedByAdmin): void{ $this->isAcceptedByAdmin = $isAcceptedByAdmin; }

    /*public function __set(string $name, $value): void
    {
        // TODO: Implement __set() method.
        switch($name) {
            case "Project name":
                $this->projectName = $value;
            case "Project leader":
                $this->projectLeader = $value;
            case "Start time":
                $this->startTime = $value;
            case "Finish time":
                $this->finishTime = $value;
            case "Status":
                $this->status = $value;
            case "Customer":
                $this->customer = $value;
            case "Is accepted by admin":
                $this->isAcceptedByAdmin = $value;
        }

    }*/

}