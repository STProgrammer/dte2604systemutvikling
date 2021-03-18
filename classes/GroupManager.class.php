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

    /////////////////////////////////////////////////////////////////////////////
    /// PROJECTS
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

    /////////////////////////////////////////////////////////////////////////////
    /// ERROR
    /////////////////////////////////////////////////////////////////////////////

    private function NotifyUser($strHeader, $strMessage)
    {
        $this->session->getFlashBag()->add('header', $strHeader);
        $this->session->getFlashBag()->add('message', $strMessage);
    }

    public function newGroup(Group $group): int //returns error code
    {

        try {
            $stmt = $this->db->prepare("INSERT INTO `Groups` (groupID, groupName, isAdmin, groupLeader)
              VALUES (:groupID, :groupName, :isAdmin, :groupLeader)");

            $stmt->bindParam(':groupID', $_POST[$group->getGroupID()], PDO::PARAM_STR, 100);
            $stmt->bindParam(':groupName', $_POST[$group->getGroupName()], PDO::PARAM_STR, 100);
            $stmt->bindParam(':isAdmin', $_POST[$group->getIsAdmin()], PDO::PARAM_INT, 100);
            $stmt->bindParam(':groupLeader', $_POST[$group->getGroupLeader()], PDO::PARAM_STR, 100);

            if ($stmt->execute()) {
                echo '<p>Gruppe lagt til.</p>';
                return 1;
            } else {
                echo '<p>Gruppe ikke lagt til</p>';
                return 2;
            }
        } catch (PDOException $e) {
            echo "ERROR" . $e->getMessage();
            return 0;
        }

    }

    public function editGroup()
    {
    }

    public function deleteGroup()
    {
    }

    public function getGroup(int $groupID)
    {
    }

    public function addEmployee(User $user)
    {
    }

    public function assignLeader(User $leader)
    {
    }

}