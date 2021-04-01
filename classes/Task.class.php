<?php


class Task {
    private $taskID;
    private $category;
    private $phaseID;
    private $groupID;
    private $parentTask;
    private $taskName;
    private $startTime;
    private $finishTime;
    private $status;
    private $projectName;
    private $timeSpent;
    private $estimatedTime;
    private bool $hasSubtask;
    private $mainResponsible;

    public function __construct() {
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
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param mixed $category
     */
    public function setCategory($category): void
    {
        $this->category = $category;
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
    public function getGroupID()
    {
        return $this->groupID;
    }

    /**
     * @param mixed $groupID
     */
    public function setGroupID($groupID): void
    {
        $this->groupID = $groupID;
    }

    /**
     * @return mixed
     */
    public function getParentTask()
    {
        return $this->parentTask;
    }

    /**
     * @param mixed $parentTask
     */
    public function setParentTask($parentTask): void
    {
        $this->parentTask = $parentTask;
    }

    /**
     * @return mixed
     */
    public function getTaskName()
    {
        return $this->taskName;
    }

    /**
     * @param mixed $taskName
     */
    public function setTaskName($taskName): void
    {
        $this->taskName = $taskName;
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
    public function getTimeSpent()
    {
        return $this->timeSpent;
    }

    /**
     * @param mixed $timeSpent
     */
    public function setTimeSpent($timeSpent): void
    {
        $this->timeSpent = $timeSpent;
    }

    /**
     * @return mixed
     */
    public function getEstimatedTime()
    {
        return $this->estimatedTime;
    }

    /**
     * @param mixed $estimatedTime
     */
    public function setEstimatedTime($estimatedTime): void
    {
        $this->estimatedTime = $estimatedTime;
    }

    /**
     * @return bool
     */
    public function hasSubtask(): bool
    {
        return $this->hasSubtask;
    }

    /**
     * @param bool $hasSubtask
     */
    public function setHasSubtask(bool $hasSubtask): void
    {
        $this->hasSubtask = $hasSubtask;
    }

    /**
     * @return mixed
     */
    public function getMainResponsible()
    {
        return $this->mainResponsible;
    }

    /**
     * @param mixed $mainResponsible
     */
    public function setMainResponsible($mainResponsible): void
    {
        $this->mainResponsible = $mainResponsible;
    }
}