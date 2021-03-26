<?php


class Hour {
    private $hourID;
    private $taskID;
    private $WhoWorked;
    private $startTime;
    private $endTime;
    private $activated;
    private $location;
    private $phaseID;
    private $absenceType;
    private $overtimeType;
    private $comment;
    private $isChanged;
    private $stampingStatus;

    public function __construct() {
    }

    //Getters
    public function getHourID(): mixed { return $this->hourID; }
    public function getTaskID(): mixed { return $this->taskID; }
    public function getWhoWorked(): mixed { return $this->WhoWorked; }
    public function getStartTime(): mixed { return $this->startTime; }
    public function getEndTime(): mixed { return $this->endTime; }
    public function getActivated(): mixed { return $this->activated; }
    public function getLocation(): mixed { return $this->location; }
    public function getPhaseID(): mixed { return $this->phaseID; }
    public function getAbsenceType(): mixed { return $this->absenceType; }
    public function getOvertimeType(): mixed { return $this->overtimeType; }
    public function getComment(): mixed { return $this->comment; }
    public function getIsChanged(): mixed { return $this->isChanged; }
    public function getStampingStatus(): mixed { return $this->stampingStatus; }

    //Setters
    public function setHourID(mixed $hourID): void { $this->hourID = $hourID; }
    public function setTaskID(mixed $taskID): void { $this->taskID = $taskID; }
    public function setWhoWorked(mixed $WhoWorked): void { $this->WhoWorked = $WhoWorked; }
    public function setStartTime(mixed $startTime): void { $this->startTime = $startTime; }
    public function setEndTime(mixed $endTime): void { $this->endTime = $endTime; }
    public function setActivated(mixed $activated): void { $this->activated = $activated; }
    public function setLocation(mixed $location): void { $this->location = $location; }
    public function setPhaseID(mixed $phaseID): void { $this->phaseID = $phaseID; }
    public function setAbsenceType(mixed $absenceType): void { $this->absenceType = $absenceType; }
    public function setOvertimeType(mixed $overtimeType): void { $this->overtimeType = $overtimeType; }
    public function setComment(mixed $comment): void { $this->comment = $comment; }
    public function setIsChanged(mixed $isChanged): void { $this->isChanged = $isChanged; }
    public function setStampingStatus(mixed $stampingStatus): void { $this->stampingStatus = $stampingStatus; }

}