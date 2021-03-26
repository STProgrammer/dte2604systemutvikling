<?php

class Project {
    private String $projectName;
    private String $projectLeader;
    private String $startTime;
    private String $finishTime;
    private int $status;
    private int $customer;
    private bool $isAcceptedByAdmin;

    public function __construct() {
    }

    /**
     * @return String
     */
    public function getProjectName(): string
    {
        return $this->projectName;
    }

    /**
     * @param String $projectName
     */
    public function setProjectName(string $projectName): void
    {
        $this->projectName = $projectName;
    }

    /**
     * @return String
     */
    public function getProjectLeader(): string
    {
        return $this->projectLeader;
    }

    /**
     * @param String $projectLeader
     */
    public function setProjectLeader(string $projectLeader): void
    {
        $this->projectLeader = $projectLeader;
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
    public function getFinishTime(): string
    {
        return $this->finishTime;
    }

    /**
     * @param String $finishTime
     */
    public function setFinishTime(string $finishTime): void
    {
        $this->finishTime = $finishTime;
    }

    /**
     * @return int
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * @param int $status
     */
    public function setStatus(int $status): void
    {
        $this->status = $status;
    }

    /**
     * @return int
     */
    public function getCustomer(): int
    {
        return $this->customer;
    }

    /**
     * @param int $customer
     */
    public function setCustomer(int $customer): void
    {
        $this->customer = $customer;
    }

    /**
     * @return bool
     */
    public function isAcceptedByAdmin(): bool
    {
        return $this->isAcceptedByAdmin;
    }

    /**
     * @param bool $isAcceptedByAdmin
     */
    public function setIsAcceptedByAdmin(bool $isAcceptedByAdmin): void
    {
        $this->isAcceptedByAdmin = $isAcceptedByAdmin;
    }






}