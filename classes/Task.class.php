<?php


class Task {
    private $taskID;
    private $category;
    private $parentTask;
    private $taskName;
    private $startTime;
    private $finishTime;
    private $status;
    private $projectName;
    private $estimatedTime;
    private $timeLeft;
    private $hasSubtasks;

    public function __construct() {
    }

    //Getters
    public function getTaskID(): mixed { return $this->taskID; }
    public function getCategory(): mixed { return $this->category; }
    public function getParentTask(): mixed { return $this->parentTask; }
    public function getTaskName(): mixed { return $this->taskName; }
    public function getStartTime(): mixed { return $this->startTime; }
    public function getFinishTime(): mixed { return $this->finishTime; }
    public function getStatus(): mixed { return $this->status; }
    public function getProjectName(): mixed { return $this->projectName; }
    public function getEstimatedTime(): mixed { return $this->estimatedTime; }
    public function getTimeLeft(): mixed { return $this->timeLeft; }
    public function getHasSubtasks(): mixed { return $this->hasSubtasks; }

    //Setters
    public function setTaskID(mixed $taskID): void { $this->taskID = $taskID; }
    public function setCategory(mixed $category): void { $this->category = $category; }
    public function setParentTask(mixed $parentTask): void { $this->parentTask = $parentTask; }
    public function setTaskName(mixed $taskName): void { $this->taskName = $taskName; }
    public function setStartTime(mixed $startTime): void { $this->startTime = $startTime; }
    public function setFinishTime(mixed $finishTime): void { $this->finishTime = $finishTime; }
    public function setStatus(mixed $status): void { $this->status = $status; }
    public function setProjectName(mixed $projectName): void { $this->projectName = $projectName; }
    public function setEstimatedTime(mixed $estimatedTime): void { $this->estimatedTime = $estimatedTime; }
    public function setTimeLeft(mixed $timeLeft): void { $this->timeLeft = $timeLeft; }
    public function setHasSubtasks(mixed $hasSubtasks): void { $this->hasSubtasks = $hasSubtasks; }

}