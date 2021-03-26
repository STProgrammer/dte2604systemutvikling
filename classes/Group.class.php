<?php


class Group
{
    private int $groupID;
    private String $projectName;
    private String $groupName;
    private bool $isAdmin;
    private int $groupLeader;
    private String $username;
    private String $firstName;
    private String $lastName;

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
    public function getGroupName(): string
    {
        return $this->groupName;
    }

    /**
     * @param String $groupName
     */
    public function setGroupName(string $groupName): void
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
     * @return int
     */
    public function getGroupLeader(): int
    {
        return $this->groupLeader;
    }

    /**
     * @param int $groupLeader
     */
    public function setGroupLeader(int $groupLeader): void
    {
        $this->groupLeader = $groupLeader;
    }

    /**
     * @return String
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @param String $username
     */
    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    /**
     * @return String
     */
    public function getFirstName(): string
    {
        return $this->firstName;
    }

    /**
     * @param String $firstName
     */
    public function setFirstName(string $firstName): void
    {
        $this->firstName = $firstName;
    }

    /**
     * @return String
     */
    public function getLastName(): string
    {
        return $this->lastName;
    }

    /**
     * @param String $lastName
     */
    public function setLastName(string $lastName): void
    {
        $this->lastName = $lastName;
    }

    

}