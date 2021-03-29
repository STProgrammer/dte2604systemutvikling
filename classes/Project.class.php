<?php


class Project {
    private $projectID;
    private $projectName;
    private $projectLeader;
    private $startTime;
    private $finishTime;
    private $status;
    private $customer;
    private bool $isAcceptedByAdmin;

    public function __construct() {
    }

    /**
     * @return mixed
     */
    public function getProjectID()
    {
        return $this->projectID;
    }

    /**
     * @param mixed $projectID
     */
    public function setProjectID($projectID): void
    {
        $this->projectID = $projectID;
    }

    /**
     * @return mixed
     */
    public function getProjectName()
    {
        return $this->projectName;
    }

    /**
     * @param mixed $projectName
     */
    public function setProjectName($projectName): void
    {
        $this->projectName = $projectName;
    }

    /**
     * @return mixed
     */
    public function getProjectLeader()
    {
        return $this->projectLeader;
    }

    /**
     * @param mixed $projectLeader
     */
    public function setProjectLeader($projectLeader): void
    {
        $this->projectLeader = $projectLeader;
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
    public function getFinishTime()
    {
        return $this->finishTime;
    }

    /**
     * @param mixed $finishTime
     */
    public function setFinishTime($finishTime): void
    {
        $this->finishTime = $finishTime;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param mixed $status
     */
    public function setStatus($status): void
    {
        $this->status = $status;
    }

    /**
     * @return mixed
     */
    public function getCustomer()
    {
        return $this->customer;
    }

    /**
     * @param mixed $customer
     */
    public function setCustomer($customer): void
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