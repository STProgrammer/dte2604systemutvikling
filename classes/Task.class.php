<?php


class Task {
    public $taskID;
    private $category;
    private $phaseID;
    private $groupID;
    private $parentTask;
    public $taskName;
    private $startTime;
    private $finishTime;
    private $status;
    private $projectName;
    private $timeSpent;
    private $estimatedTime;
    private bool $hasSubtask;
    private $mainResponsible;
    private $categoryName;


    public function __construct() {
    }
    public function getTaskID(){return $this->taskID;}
    public function setTaskID($taskID): void{$this->taskID = $taskID;}
    public function getCategory(){return $this->category;}
    public function setCategory($category): void{$this->category = $category;}
    public function getPhaseID(){return $this->phaseID;}
    public function setPhaseID($phaseID): void{$this->phaseID = $phaseID;}
    public function getGroupID(){return $this->groupID;}
    public function setGroupID($groupID): void{$this->groupID = $groupID;}
    public function getParentTask(){return $this->parentTask;}
    public function setParentTask($parentTask): void{$this->parentTask = $parentTask;}
    public function getTaskName(){return $this->taskName;}
    public function setTaskName($taskName): void{$this->taskName = $taskName;}
    public function getStartTime(){return $this->startTime;}
    public function setStartTime($startTime): void{$this->startTime = $startTime;}
    public function getFinishTime(){return $this->finishTime;}
    public function setFinishTime($finishTime): void{$this->finishTime = $finishTime;}
    public function getStatus(){return $this->status;}
    public function setStatus($status): void{$this->status = $status;}
    public function getProjectName(){return $this->projectName;}
    public function setProjectName($projectName): void{$this->projectName = $projectName;}
    public function getTimeSpent(){return $this->timeSpent;}
    public function setTimeSpent($timeSpent): void{$this->timeSpent = $timeSpent;}
    public function getEstimatedTime(){return $this->estimatedTime;}
    public function setEstimatedTime($estimatedTime): void{$this->estimatedTime = $estimatedTime;}
    public function hasSubtask(): bool{return $this->hasSubtask;}
    public function setHasSubtask(bool $hasSubtask): void{$this->hasSubtasks = $hasSubtask;}
    public function getMainResponsible(){return $this->mainResponsible;}
    public function setMainResponsible($mainResponsible): void{$this->mainResponsible = $mainResponsible;}
    public function getCategoryName(){return $this->categoryName;}
    public function setCategoryName($categoryName): void{$this->categoryName = $categoryName;}
}