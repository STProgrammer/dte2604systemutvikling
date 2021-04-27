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
        //$this->session->getFlashBag()->clear();
        $this->session->getFlashBag()->add('header', $strHeader);
        $this->session->getFlashBag()->add('message', $strMessage);
    }

    /////////////////////////////////////////////////////////////////////////////
    /// GROUPS
    /////////////////////////////////////////////////////////////////////////////

    public function getAllGroups(): array
    {
        try {
            $stmt = $this->db->prepare('SELECT Groups.*, CONCAT(groupLeader.firstName, " ", groupLeader.lastName, " (", groupLeader.username, ")") as leaderName,
       count(UsersAndGroups.groupID) as nrOfMembers
FROM Groups
    LEFT JOIN UsersAndGroups ON Groups.groupID = UsersAndGroups.groupID
LEFT JOIN Users as groupLeader on groupLeader.userID = Groups.groupLeader GROUP BY Groups.groupID ORDER BY Groups.groupName ASC;');
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

    //GET GROUPS OF USER
    public function getGroupsOfUser($userId): array
    {
        try {
            $stmt = $this->db->prepare('SELECT Groups.*, CONCAT(groupLeader.firstName, " ", groupLeader.lastName, " (", groupLeader.username, ")") as leaderName,
       count(UsersAndGroups.groupID) as nrOfMembers
FROM Groups
    LEFT JOIN UsersAndGroups ON Groups.groupID = UsersAndGroups.groupID
LEFT JOIN Users as groupLeader on groupLeader.userID = Groups.groupLeader 
WHERE UsersAndGroups.userID = :userID OR Groups.groupLeader = :userID 
GROUP BY Groups.groupID ORDER BY Groups.groupName ASC;');
            $stmt->bindParam(":userID", $userId, PDO::PARAM_INT);
            $stmt->execute();
            if ($groups = $stmt->fetchAll(PDO::FETCH_CLASS, "Group")) {
                return $groups;
            } else {
                $this->notifyUser("Groups not found", "Kunne ikke hente mine grupper");
                return array();
            }
        } catch (Exception $e) {
            $this->NotifyUser("En feil oppstod, på getGroupsOfUser()", $e->getMessage());
            return array();
        }
    }




    public function newGroup(): bool //returns boolean value
    {
        $groupName = $this->request->request->get('groupName');
        $isAdmin = $this->request->request->getInt('isAdmin', 0);
        try {
            $stmt = $this->db->prepare("INSERT INTO `Groups` (groupName, isAdmin)
              VALUES (:groupName, :isAdmin);");
            $stmt->bindParam(':groupName', $groupName, PDO::PARAM_STR, 100);
            $stmt->bindParam(':isAdmin', $isAdmin, PDO::PARAM_INT, 100);
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


    public function editGroup(Group $group): bool
    {
        $groupID = $group->getGroupID();
        $groupName = $this->request->request->get('groupName', $group->getGroupName());
        $groupLeader = $this->request->request->get('groupLeader', null);
        $oldGroupLeader = $group->getGroupLeader();
        $isAdmin = $this->request->request->getInt('isAdmin', $group->isAdmin());
        $projectName = $group->getProjectName();
        try {
            $sth = $this->db->prepare("update Groups set groupName = :groupName, groupLeader = :groupLeader, 
    isAdmin = :isAdmin WHERE groupID = :groupID AND NOT EXISTS
    (SELECT projectLeader FROM Projects WHERE projectLeader = :groupLeader AND projectName = :projectName);
      UPDATE Users SET Users.isGroupLeader = 0
WHERE NOT EXISTS
  (SELECT groupLeader FROM Groups WHERE groupLeader = :oldGroupLeader) AND Users.userID = :oldGroupLeader;
      UPDATE Users SET Users.isGroupLeader = 1 WHERE Users.userID = :groupLeader;");
            $sth->bindParam(':groupID', $groupID, PDO::PARAM_INT);
            $sth->bindParam(':groupName', $groupName, PDO::PARAM_STR);
            $sth->bindParam(':projectName', $projectName, PDO::PARAM_STR);
            $sth->bindParam(':groupLeader', $groupLeader, PDO::PARAM_INT);
            $sth->bindParam(':isAdmin', $isAdmin, PDO::PARAM_INT);
            $sth->bindParam(':oldGroupLeader', $oldGroupLeader, PDO::PARAM_INT);
            if ($sth->execute()) {
                $sth->closeCursor();
                $this->notifyUser('Group details changed');
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


    public function deleteGroup(Group $group): bool
    {
        $groupID = $group->getGroupID();
        $groupLeader = $group->getGroupLeader();
        try {
            $stmt = $this->db->prepare("DELETE FROM Groups WHERE groupID = :groupID;
                                    DELETE FROM UsersAndGroups WHERE groupID = :groupID;
                                    UPDATE Users SET Users.isGroupLeader = 0
                                    WHERE NOT EXISTS
                                    (SELECT groupLeader FROM Groups WHERE groupLeader = :groupLeader) AND Users.userID = :groupLeader;");
            $stmt->bindParam(':groupID', $groupID, PDO::PARAM_INT);
            $stmt->bindParam(':groupLeader', $groupLeader, PDO::PARAM_INT);
            $stmt->execute();
            if ($stmt->rowCount() >= 1) {
                $this->notifyUser("Group deleted");
                return true;
            } else {
                $this->notifyUser("Failed to delete group!");
                return false;
            }
        } catch (Exception $e) {
            $this->notifyUser("Failed to delete group!", $e->getMessage());
            return false;
        }

    }

    public function getGroup(int $groupID)
    {
        try {
            $stmt = $this->db->prepare('SELECT Groups.*, CONCAT(Users.firstName, " ", Users.lastName, " ", " (", Users.username, ")") as leaderName FROM Groups LEFT JOIN 
    Users ON Groups.groupLeader=Users.userID WHERE groupID = :groupID ORDER BY `groupName` ASC;');
            $stmt->bindParam(':groupID', $groupID, PDO::PARAM_INT, 100);
            $stmt->execute();
            if ($group = $stmt->fetchObject("Group")) {
                return $group;
            } else {
                $this->notifyUser("Ingen grupper funnet", "Kunne ikke hente gruppe");
                return null;
            }
        } catch (Exception $e) {
            $this->NotifyUser("En feil oppstod, på getAllGroups()", $e->getMessage());
            return null;
        }
    }

    public function addEmployees($groupID)
    {
        $users = $this->request->request->get('groupMembers');
        try {
            $stmt = $this->db->prepare("INSERT IGNORE INTO UsersAndGroups (groupID, userID) VALUES (:groupID, :userID);");
            if (is_array($users)) {
                foreach ($users as $userID) {
                    $stmt->bindParam(':groupID', $groupID, PDO::PARAM_INT);
                    $stmt->bindParam(':userID', $userID, PDO::PARAM_INT);
                    $stmt->execute();
                }
                $this->notifyUser("Medlemmer ble lagt til");
            } else {
                $this->notifyUser("Fikk ikke legge til brukere");
                return false;
            }
        } catch (Exception $e) {
            $this->notifyUser("Fikk ikke legge til brukere", $e->getMessage());
            return false;
        }
        return true;
    }

    public function addToProject(Group $group) : bool
    {
        $projectName = $this->request->get('projectName');
        $groupID = $group->getGroupID();
        $groupLeader = $group->getGroupLeader();
        try {
            $stmt = $this->db->prepare("UPDATE Groups SET projectName = :projectName WHERE groupID = :groupID;
    (SELECT projectLeader FROM Projects WHERE projectLeader = :groupLeader AND projectName = :projectName);
UPDATE Groups SET groupLeader = null WHERE EXISTS (SELECT projectLeader FROM Projects WHERE projectLeader = :groupLeader AND projectName = :projectName) AND groupID = :groupID;
                                    UPDATE Users SET Users.isGroupLeader = 0
                                    WHERE NOT EXISTS
                                    (SELECT groupLeader FROM Groups WHERE groupLeader = :groupLeader) AND Users.userID = :groupLeader;");
            $stmt->bindParam(':groupID', $groupID, PDO::PARAM_INT);
            $stmt->bindParam(':groupLeader', $groupLeader, PDO::PARAM_INT);
            $stmt->bindParam(':projectName', $projectName, PDO::PARAM_STR);
            if ($stmt->execute()) {
                $this->notifyUser("Gruppe ble lagt til prosjektet");
                return true;
            } else {
                $this->notifyUser("Fikk ikke legge til prosjektet");
                return false;
            }
        } catch (Exception $e) {
            $this->notifyUser("Fikk ikke legge til prosjektet", $e->getMessage());
            return false;
        }
    }

    public function removeAllEmployees(Group $group)
    {
        $groupID = $group->getGroupID();
        $groupLeader = $group->getGroupLeader();
        try {
            $stmt = $this->db->prepare("DELETE FROM UsersAndGroups WHERE groupID = :groupID;
UPDATE Groups SET Groups.groupLeader = null WHERE groupID = :groupID;
    UPDATE Users SET Users.isGroupLeader = 0
WHERE NOT EXISTS
  (SELECT groupLeader FROM Groups WHERE groupLeader = :groupLeader) AND Users.userID = :groupLeader;");
            $stmt->bindParam(':groupID', $groupID, PDO::PARAM_INT);
            $stmt->bindParam(':groupLeader', $groupLeader, PDO::PARAM_INT);
            $stmt->execute();
        } catch (Exception $e) {
            $this->notifyUser("Failed to add employees", $e->getMessage());
            return false;
        }
        return true;
    }


    public function removeEmployees(Group $group)
    {
        $users = $this->request->request->get('groupMembers');
        $groupID = $group->getGroupID();
        $groupLeader = $group->getGroupLeader();
        try {
            $stmt = $this->db->prepare("DELETE FROM UsersAndGroups WHERE groupID = :groupID AND userId = :userID;
UPDATE Groups SET Groups.groupLeader = NULL WHERE groupID = :groupID AND Groups.groupLeader = :userID;
    UPDATE Users SET Users.isGroupLeader = 0
WHERE NOT EXISTS
  (SELECT groupLeader FROM Groups WHERE groupLeader = :groupLeader) AND Users.userID = :groupLeader;");
            if (is_array($users)) {
                foreach ($users as $userID) {
                    $stmt->bindParam(':groupID', $groupID, PDO::PARAM_INT);
                    $stmt->bindParam(':userID', $userID, PDO::PARAM_INT);
                    $stmt->bindParam(':groupLeader', $groupLeader, PDO::PARAM_INT);
                    $stmt->execute();
                    $stmt->closeCursor();
                }
            } else {
                $this->notifyUser("Ikke klarte å fjerning brukere");
                return false;
            }
        } catch (Exception $e) {
            $this->notifyUser("Feil med fjerning av brukere", $e->getMessage());
            return false;
        }
        return true;
    }

    public function getGroupMembers($groupID): array
    {
        $members = array();
        try {
            $stmt = $this->db->prepare("SELECT Users.*, CONCAT(Users.firstName, ' ', Users.lastName,  ' ( ', Users.username,  ') ') as fullName FROM Users WHERE EXISTS(SELECT UsersAndGroups.userID FROM UsersAndGroups WHERE UsersAndGroups.groupID = :groupID AND Users.userID = UsersAndGroups.userID);");
            $stmt->bindParam(':groupID', $groupID, PDO::PARAM_INT, 100);
            $stmt->execute();
            if ($members = $stmt->fetchAll(PDO::FETCH_CLASS, 'User')) {
                return $members;
            } else {
                $this->notifyUser("Ingen medlemmer funnet", "Kunne ikke hente medlemmer av gruppa");
                return array();
            }
        } catch (Exception $e) {
            $this->NotifyUser("En feil oppstod, på getGroupMembers()", $e->getMessage());
            return array();
        }
    }

    public function getAllNonMembers($groupID): array
    {
        $nonmembers = array();
        try {
            $stmt = $this->db->prepare("SELECT * FROM Users WHERE NOT EXISTS(SELECT UsersAndGroups.userID FROM UsersAndGroups WHERE UsersAndGroups.groupID = :groupID AND Users.userID = UsersAndGroups.userID) AND Users.userType > 0 ORDER BY Users.lastName;");
            $stmt->bindParam(':groupID', $groupID, PDO::PARAM_INT, 100);
            $stmt->execute();
            if ($nonmembers = $stmt->fetchAll(PDO::FETCH_CLASS, 'User')) {
                return $nonmembers;
            } else {
                $this->notifyUser("Ingen medlemmer funnet", "Kunne ikke hente medlemmer av gruppa");
                return array();
            }
        } catch (Exception $e) {
            $this->NotifyUser("En feil oppstod, på getGroupMembers()", $e->getMessage());
            return array();
        }
    }


    public function getLeaderCandidates(int $groupID) : array
    {
        $candidates = array();
        try {
            $stmt = $this->db->prepare("SELECT Users.*, Groups.groupID
FROM Users
JOIN UsersAndGroups ON Users.userID = UsersAndGroups.userID
JOIN Groups ON UsersAndGroups.groupID = Groups.groupID
WHERE Groups.groupID = :groupID 
AND NOT EXISTS (SELECT Projects.projectLeader FROM Projects WHERE Projects.projectLeader = Users.userID AND Projects.projectName = Groups.projectName) ORDER BY Users.lastName;");
            $stmt->bindParam(':groupID', $groupID, PDO::PARAM_INT, 100);
            $stmt->execute();
            if ($candidates = $stmt->fetchAll(PDO::FETCH_CLASS, 'User')) {
                return $candidates;
            } else {
                $this->notifyUser("Ingen kandidater funnet", "Kunne ikke hente kandidater for gruppeleder");
                return $candidates;
            }
        } catch (Exception $e) {
            $this->NotifyUser("En feil oppstod, på getLeaderCandidates()", $e->getMessage());
            return $candidates;
        }
    }

}