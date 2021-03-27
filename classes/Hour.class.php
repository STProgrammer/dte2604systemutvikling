<?php


class Hour {
    private $hourID;
    private $taskID;
    private $whoWorked;
    private $startTime;
    private $endTime;
    private bool $activated;
    private $location;
    private $phaseID;
    private $absenceType;
    private $overtimeType;
    private $comment;
    private bool $isChanged;
    private bool $stampingStatus;

    public function __construct() {
    }

    /**
     * @return mixed
     */
    public function getHourID()
    {
        return $this->hourID;
    }

    /**
     * @param mixed $hourID
     */
    public function setHourID($hourID): void
    {
        $this->hourID = $hourID;
    }

    /**
     * @return mixed
     */
    public function getTaskID()
    {
        return $this->taskID;
    }

    /**
     * @param mixed $taskID
     */
    public function setTaskID($taskID): void
    {
        $this->taskID = $taskID;
    }

    /**
     * @return mixed
     */
    public function getWhoWorked()
    {
        return $this->whoWorked;
    }

    /**
     * @param mixed $whoWorked
     */
    public function setWhoWorked($whoWorked): void
    {
        $this->whoWorked = $whoWorked;
    }

    /**
     * @return mixed
     */
    public function getStartTime()
    {
        return $this->startTime;
    }

    /**
     * @param mixed $startTime
     */
    public function setStartTime($startTime): void
    {
        $this->startTime = $startTime;
    }

    /**
     * @return mixed
     */
    public function getEndTime()
    {
        return $this->endTime;
    }

    /**
     * @param mixed $endTime
     */
    public function setEndTime($endTime): void
    {
        $this->endTime = $endTime;
    }

    /**
     * @return bool
     */
    public function isActivated(): bool
    {
        return $this->activated;
    }

    /**
     * @param bool $activated
     */
    public function setActivated(bool $activated): void
    {
        $this->activated = $activated;
    }

    /**
     * @return mixed
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * @param mixed $location
     */
    public function setLocation($location): void
    {
        $this->location = $location;
    }

    /**
     * @return mixed
     */
    public function getPhaseID()
    {
        return $this->phaseID;
    }

    /**
     * @param mixed $phaseID
     */
    public function setPhaseID($phaseID): void
    {
        $this->phaseID = $phaseID;
    }

    /**
     * @return mixed
     */
    public function getAbsenceType()
    {
        return $this->absenceType;
    }

    /**
     * @param mixed $absenceType
     */
    public function setAbsenceType($absenceType): void
    {
        $this->absenceType = $absenceType;
    }

    /**
     * @return mixed
     */
    public function getOvertimeType()
    {
        return $this->overtimeType;
    }

    /**
     * @param mixed $overtimeType
     */
    public function setOvertimeType($overtimeType): void
    {
        $this->overtimeType = $overtimeType;
    }

    /**
     * @return mixed
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * @param mixed $comment
     */
    public function setComment($comment): void
    {
        $this->comment = $comment;
    }

    /**
     * @return bool
     */
    public function isChanged(): bool
    {
        return $this->isChanged;
    }

    /**
     * @param bool $isChanged
     */
    public function setIsChanged(bool $isChanged): void
    {
        $this->isChanged = $isChanged;
    }

    /**
     * @return bool
     */
    public function isStampingStatus(): bool
    {
        return $this->stampingStatus;
    }

    /**
     * @param bool $stampingStatus
     */
    public function setStampingStatus(bool $stampingStatus): void
    {
        $this->stampingStatus = $stampingStatus;
    }

}