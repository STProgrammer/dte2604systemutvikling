<?php


use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

class GroupManager
{
    private $db;
    private $request;
    private $session;

    //CONSTRUCTOR//
    function __construct(PDO $db, Request $request, Session $session)
    {
        $this->db = $db;
        $this->request = $request;
        $this->session = $session;
    }


    private function NotifyUser($strHeader, $strMessage = "")
    {
        $this->session->getFlashBag()->clear();
        $this->session->getFlashBag()->add('header', $strHeader);
        $this->session->getFlashBag()->add('message', $strMessage);
    }

    /////////////////////////////////////////////////////////////////////////////
    /// GROUPS
    /////////////////////////////////////////////////////////////////////////////

    public function getAllGroups(): array
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM Groups ORDER BY `groupName` ASC");
            $stmt->execute();
            if ($groups = $stmt->fetchAll(PDO::FETCH_CLASS, "Group")) {
                return $groups;
            } else {
                $this->notifyUser("Groups not found", "Kunne ikke hente grupper");
                return array();
            }
        } catch (Exception $e) {
            $this->NotifyUser("En feil oppstod, på getAllGroups()", $e->getMessage());
            return array();
        }
    }



    public function newGroup(): bool //returns boolean value
    {
        $groupName = $this->request->request->get('groupName');
        $isAdmin = $this->request->request->getInt('isAdmin', 0);
        $groupLeader = $this->request->request->getInt('GroupLeader');
        try {
            $stmt = $this->db->prepare("INSERT INTO `Groups` (groupName, isAdmin, groupLeader)
              VALUES (:groupName, :isAdmin, :groupLeader); 
              UPDATE Users SET isGroupLeader = 1 WHERE UserID = :groupLeader;");
            $stmt->bindParam(':groupName', $groupName, PDO::PARAM_STR, 100);
            $stmt->bindParam(':isAdmin', $isAdmin, PDO::PARAM_INT, 100);
            $stmt->bindParam(':groupLeader',  $groupLeader, PDO::PARAM_INT, 100);
            if ($stmt->execute()) {
                $this->NotifyUser("Group added");
                return true;
            } else {
                $this->NotifyUser("Failed to add group");
                return false;
            }
        } catch (PDOException $e) {
            $this->NotifyUser("Failed to add group", $e->getMessage());
            return false;
        }
    }


    public function editGroup(Group $group) : bool
    {
        $groupID = $group->getGroupID();
        $groupName = $this->request->request->get('groupName', $group->getGroupName());
        $groupLeader = $this->request->request->getInt('groupLeader', $group->getGroupLeader());
        $oldGroupLeader = $group->getGroupLeader();
        $isAdmin = $this->request->request->getInt('isAdmin', $group->isAdmin());
        try {
            $sth = $this->dbase->prepare("update Users set groupName = :groupName, groupLeader = :groupLeader, 
    isAdmin = :isAdmin WHERE groupID = :groupID;
    UPDATE Users SET Users.isGroupLeader = 0
WHERE NOT EXISTS
  (SELECT groupLeader FROM Groups WHERE groupLeader = :oldGroupLeader) AND Users.userID = :oldGroupLeader;");
            $sth->bindParam(':groupID', $groupID, PDO::PARAM_INT);
            $sth->bindParam(':groupName', $groupName, PDO::PARAM_STR);
            $sth->bindParam(':groupLeader', $groupLeader, PDO::PARAM_INT);
            $sth->bindParam(':isAdmin', $isAdmin, PDO::PARAM_INT);
            $sth->bindParam(':oldGroupLeader', $oldGroupLeader, PDO::PARAM_INT);
            $sth->execute();
            if ($sth->rowCount() >= 1) {
                $this->notifyUser('Group details changed');
                $this->updateLeadershipStatus();
                return true;
            } else {
                $this->notifyUser('Failed to change group details');
                return false;
            }
        } catch (Exception $e) {
            $this->notifyUser("Failed to change group details", $e->getMessage());
            return false;
        }
    }


    public function deleteGroup(Group $group) : bool
    {
        $groupID = $group->getGroupID();
        $groupLeader = $group->getGroupLeader();
        try
        {
            $stmt = $this->dbase->prepare("DELETE FROM Groups WHERE groupID = :groupID;
    UPDATE Users SET Users.isGroupLeader = 0
WHERE NOT EXISTS
  (SELECT groupLeader FROM Groups WHERE groupLeader = :groupLeader) AND Users.userID = :groupLeader;");
            $stmt->bindParam(':groupID', $groupID, PDO::PARAM_INT);
            $stmt->bindParam(':groupLeader', $groupLeader, PDO::PARAM_INT);
            $stmt->execute();
            if ($stmt->rowCount() >= 1) {
                $this->notifyUser( "Group deleted");
                return true;
            } else {
                $this->notifyUser( "Failed to delete group!");
                return false;
            }
        }
        catch (Exception $e) {
            $this->notifyUser( "Failed to delete group!", $e->getMessage());
            return false;
        }

    }

    public function getGroup(int $groupID)
    {
    }

    public function addEmployee(User $user)
    {
    }

}