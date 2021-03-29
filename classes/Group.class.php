<?php


class Group
{
    private $groupID;
    private $projectName;
    private $groupName;
    private bool $isAdmin;
    private $groupLeader;

    /**
     * Group constructor.
     */
    public function __construct()
    {
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
    public function getGroupName()
    {
        return $this->groupName;
    }

    /**
     * @param mixed $groupName
     */
    public function setGroupName($groupName): void
    {
        $this->groupName = $groupName;
    }

    /**
     * @return bool
     */
    public function isAdmin(): bool
    {
        return $this->isAdmin;
    }

    /**
     * @param bool $isAdmin
     */
    public function setIsAdmin(bool $isAdmin): void
    {
        $this->isAdmin = $isAdmin;
    }

    /**
     * @return mixed
     */
    public function getGroupLeader()
    {
        return $this->groupLeader;
    }

    /**
     * @param mixed $groupLeader
     */
    public function setGroupLeader($groupLeader): void
    {
        $this->groupLeader = $groupLeader;
    }




}