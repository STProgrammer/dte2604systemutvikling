<?php

class Project {
    private $projectName;
    private $projectLeader;
    private $startTime;
    private $finishTime;
    private $status;
    private $customer;

    public function __construct(string $projectName, int $projectLeader,  string $startTime,
                                string $finishTime, int $status, int $customer, array $row) {
        $this->projectName = $row['Project name'];
        $this->projectLeader = $row['Project leader'];
        $this->startTime = $row['Start time'];
        $this->finishTime = $row['Finish time'];
        $this->status = $row['Status'];
        $this->customer = $row['Customer'];
    }

    //Getters
    public function getProjectName(): mixed { return $this->projectName; }
    public function getProjectLeader(): mixed { return $this->projectLeader; }
    public function getStartTime(): mixed { return $this->startTime; }
    public function getFinishTime(): mixed { return $this->finishTime; }
    public function getStatus(): mixed { return $this->status; }
    public function getCustomer(): mixed { return $this->customer; }

    //Setters
    public function setProjectName(mixed $projectName): void { $this->projectName = $projectName; }
    public function setProjectLeader(mixed $projectLeader): void { $this->projectLeader = $projectLeader; }
    public function setStartTime(mixed $startTime): void { $this->startTime = $startTime; }
    public function setFinishTime(mixed $finishTime): void { $this->finishTime = $finishTime; }
    public function setStatus(mixed $status): void { $this->status = $status; }
    public function setCustomer(mixed $customer): void { $this->customer = $customer; }






















}