<?php


class Hour {
    private $hourID;
    private $taskID;
    private $whoWorked;
    private $startTime;
    private $endTime;
    private $timeWorked;
    private bool $activated;
    private $location;
    private $phaseID;
    private $absenceType;
    private $overtimeType;
    private $comment;
    private $commentBoss;
    private bool $isChanged;
    private bool $stampingStatus;
    private $taskType;

    public function __construct() {
    }

    public function getHourID(){return $this->hourID;}
    public function getTaskID(){return $this->taskID;}
    public function getWhoWorked(){return $this->whoWorked;}
    public function getStartTime(){return $this->startTime;}
    public function getEndTime(){return $this->endTime;}
    public function getTimeWorked(){return $this->timeWorked;}
    public function getLocation(){return $this->location;}
    public function getPhaseID(){return $this->phaseID;}
    public function getAbsenceType(){return $this->absenceType;}
    public function getOvertimeType(){return $this->overtimeType;}
    public function getComment(){return $this->comment;}
    public function getCommentBoss(){return $this->commentBoss;}
    public function getTaskType(){return $this->taskType;}

    public function setHourID($hourID): void{$this->hourID = $hourID;}
    public function setTaskID($taskID): void{$this->taskID = $taskID;}
    public function setWhoWorked($whoWorked): void{$this->whoWorked = $whoWorked;}
    public function setStartTime($startTime): void{$this->startTime = $startTime;}
    public function setEndTime($endTime): void{$this->endTime = $endTime;}
    public function setTimeWorked($timeWorked): void{$this->timeWorked = $timeWorked;}
    public function setActivated(bool $activated): void{$this->activated = $activated;}
    public function setLocation($location): void{$this->location = $location;}
    public function setPhaseID($phaseID): void{$this->phaseID = $phaseID;}
    public function setAbsenceType($absenceType): void{$this->absenceType = $absenceType;}
    public function setOvertimeType($overtimeType): void{$this->overtimeType = $overtimeType;}
    public function setComment($comment): void{$this->comment = $comment;}
    public function setIsChanged(bool $isChanged): void{$this->isChanged = $isChanged;}
    public function setStampingStatus(bool $stampingStatus): void{$this->stampingStatus = $stampingStatus;}
    public function setTaskType($taskType): void{$this->taskType = $taskType;}

    public function isActivated(): bool{return $this->activated == 1;}
    public function isChanged(): bool{return $this->isChanged;}
    public function isStampingStatus(): bool{return $this->stampingStatus;}

}