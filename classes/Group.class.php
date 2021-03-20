<?php


class Group
{
    private $groupID;
    private $groupName;
    private $isAdmin;
    private $groupLeader;
    private $firstName;
    private $lastName;


    public function getFirstName(){return $this->firstName;}
    public function getLastName(){return $this->lastName;}
    public function getGroupID(){return $this->groupID;}
    public function getGroupName(){return $this->groupName;}
    public function getGroupLeader(){return $this->groupLeader;}

    public function setFirstName($firstName): void{$this->firstName = $firstName;}
    public function setLastName($lastName): void{$this->lastName = $lastName;}
    public function setGroupID($groupID): void{$this->groupID = $groupID;}
    public function setGroupName($groupName): void{$this->groupName = $groupName;}
    public function setGroupLeader($groupLeader): void{$this->groupLeader = $groupLeader;}
    public function setAdmin($isAdmin): void{$this->isAdmin = $isAdmin;}

    public function isAdmin(){return $this->isAdmin;}
}