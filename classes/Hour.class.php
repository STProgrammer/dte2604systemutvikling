<?php


class Hour {
    private int $hourID;
    private int $taskID;
    private int $whoWorked;
    private String $startTime;
    private String $endTime;
    private bool $activated;
    private String $location;
    private int $phaseID;
    private String $absenceType;
    private int $overtimeType;
    private String $comment;
    private bool $isChanged;
    private bool $stampingStatus;

    public function __construct() {
    }

    /**
     * @return int
     */
    public function getHourID(): int
    {
        return $this->hourID;
    }

    /**
     * @param int $hourID
     */
    public function setHourID(int $hourID): void
    {
        $this->hourID = $hourID;
    }

    /**
     * @return int
     */
    public function getTaskID(): int
    {
        return $this->taskID;
    }

    /**
     * @param int $taskID
     */
    public function setTaskID(int $taskID): void
    {
        $this->taskID = $taskID;
    }

    /**
     * @return int
     */
    public function getWhoWorked(): int
    {
        return $this->whoWorked;
    }

    /**
     * @param int $whoWorked
     */
    public function setWhoWorked(int $whoWorked): void
    {
        $this->whoWorked = $whoWorked;
    }

    /**
     * @return String
     */
    public function getStartTime(): string
    {
        return $this->startTime;
    }

    /**
     * @param String $startTime
     */
    public function setStartTime(string $startTime): void
    {
        $this->startTime = $startTime;
    }

    /**
     * @return String
     */
    public function getEndTime(): string
    {
        return $this->endTime;
    }

    /**
     * @param String $endTime
     */
    public function setEndTime(string $endTime): void
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
     * @return String
     */
    public function getLocation(): string
    {
        return $this->location;
    }

    /**
     * @param String $location
     */
    public function setLocation(string $location): void
    {
        $this->location = $location;
    }

    /**
     * @return int
     */
    public function getPhaseID(): int
    {
        return $this->phaseID;
    }

    /**
     * @param int $phaseID
     */
    public function setPhaseID(int $phaseID): void
    {
        $this->phaseID = $phaseID;
    }

    /**
     * @return String
     */
    public function getAbsenceType(): string
    {
        return $this->absenceType;
    }

    /**
     * @param String $absenceType
     */
    public function setAbsenceType(string $absenceType): void
    {
        $this->absenceType = $absenceType;
    }

    /**
     * @return int
     */
    public function getOvertimeType(): int
    {
        return $this->overtimeType;
    }

    /**
     * @param int $overtimeType
     */
    public function setOvertimeType(int $overtimeType): void
    {
        $this->overtimeType = $overtimeType;
    }

    /**
     * @return String
     */
    public function getComment(): string
    {
        return $this->comment;
    }

    /**
     * @param String $comment
     */
    public function setComment(string $comment): void
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