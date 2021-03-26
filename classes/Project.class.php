<?php

use User;


class Project {
    private String $projectName;
    private User $projectLeader;
    private String $startTime;
    private String $finishTime;
    private int $status;
    private User $customer;
    private bool $isAcceptedByAdmin;
    private array $groups;
    private array $phases;

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
     * @return \User
     */
    public function getProjectLeader(): \User
    {
        return $this->projectLeader;
    }

    /**
     * @param \User $projectLeader
     */
    public function setProjectLeader(\User $projectLeader): void
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
     * @return \User
     */
    public function getCustomer(): \User
    {
        return $this->customer;
    }

    /**
     * @param \User $customer
     */
    public function setCustomer(\User $customer): void
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

    /**
     * @return array
     */
    public function getGroups(): array
    {
        return $this->groups;
    }

    /**
     * @param array $groups
     */
    public function setGroups(array $groups): void
    {
        $this->groups = $groups;
    }

    /**
     * @return array
     */
    public function getPhases(): array
    {
        return $this->phases;
    }

    /**
     * @param array $phases
     */
    public function setPhases(array $phases): void
    {
        $this->phases = $phases;
    }

    





}