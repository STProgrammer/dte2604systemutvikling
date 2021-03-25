<?php


class Phase
{
    private int $phaseID;
    private String $phaseName;
    private String $projectName;
    private $startTime;
    private $finishTime;
    private int $status;



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