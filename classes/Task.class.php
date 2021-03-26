<?php


class Task {
    private int $taskID;
    private String $category;
    private int $phaseID;
    private int $groupID;
    private int $parentTask;
    private String $taskName;
    private String $startTime;
    private String $finishTime;
    private int $status;
    private String $projectName;
    private int $timeSpent;
    private int $estimatedTime;
    private bool $hasSubtasks;
    private int $mainResponsible;

    public function __construct() {
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
     * @return String
     */
    public function getCategory(): string
    {
        return $this->category;
    }

    /**
     * @param String $category
     */
    public function setCategory(string $category): void
    {
        $this->category = $category;
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
     * @return int
     */
    public function getGroupID(): int
    {
        return $this->groupID;
    }

    /**
     * @param int $groupID
     */
    public function setGroupID(int $groupID): void
    {
        $this->groupID = $groupID;
    }

    /**
     * @return int
     */
    public function getParentTask(): int
    {
        return $this->parentTask;
    }

    /**
     * @param int $parentTask
     */
    public function setParentTask(int $parentTask): void
    {
        $this->parentTask = $parentTask;
    }

    /**
     * @return String
     */
    public function getTaskName(): string
    {
        return $this->taskName;
    }

    /**
     * @param String $taskName
     */
    public function setTaskName(string $taskName): void
    {
        $this->taskName = $taskName;
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
     * @return int
     */
    public function getTimeSpent(): int
    {
        return $this->timeSpent;
    }

    /**
     * @param int $timeSpent
     */
    public function setTimeSpent(int $timeSpent): void
    {
        $this->timeSpent = $timeSpent;
    }

    /**
     * @return int
     */
    public function getEstimatedTime(): int
    {
        return $this->estimatedTime;
    }

    /**
     * @param int $estimatedTime
     */
    public function setEstimatedTime(int $estimatedTime): void
    {
        $this->estimatedTime = $estimatedTime;
    }

    /**
     * @return bool
     */
    public function isHasSubtasks(): bool
    {
        return $this->hasSubtasks;
    }

    /**
     * @param bool $hasSubtasks
     */
    public function setHasSubtasks(bool $hasSubtasks): void
    {
        $this->hasSubtasks = $hasSubtasks;
    }

    /**
     * @return int
     */
    public function getMainResponsible(): int
    {
        return $this->mainResponsible;
    }

    /**
     * @param int $mainResponsible
     */
    public function setMainResponsible(int $mainResponsible): void
    {
        $this->mainResponsible = $mainResponsible;
    }



}