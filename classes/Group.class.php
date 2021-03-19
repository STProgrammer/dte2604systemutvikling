<?php


class Group
{
    private $groupID;
    private $groupName;
    private $isAdmin;
    private $groupLeader;
    private $firstName;


    /**
     * @return mixed
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @param mixed $firstName
     */
    public function setFirstName($firstName): void
    {
        $this->firstName = $firstName;
    }

    /**
     * @return mixed
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @param mixed $lastName
     */
    public function setLastName($lastName): void
    {
        $this->lastName = $lastName;
    }
    private $lastName;

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
     * @return mixed
     */
    public function isAdmin()
    {
        return $this->isAdmin;
    }

    /**
     * @param mixed $isAdmin
     */
    public function setAdmin($isAdmin): void
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