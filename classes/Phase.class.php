<?php


class Phase
{
    private int $phaseID;
    private String $phaseName;
    private String $projectName;
    private String $startTime;
    private String $finishTime;
    private int $status;

    /**
     * Phase constructor.
     */
    public function __construct()
    {
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
    public function getPhaseName(): string
    {
        return $this->phaseName;
    }

    /**
     * @param String $phaseName
     */
    public function setPhaseName(string $phaseName): void
    {
        $this->phaseName = $phaseName;
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


}