<?php


use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

class ProjectManager
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
    /// ERROR
    /////////////////////////////////////////////////////////////////////////////

    private function NotifyUser($strHeader, $strMessage = "")
    {
        //$this->session->getFlashBag()->clear();
        $this->session->getFlashBag()->add('header', $strHeader);
        $this->session->getFlashBag()->add('message', $strMessage);
    }

    /////////////////////////////////////////////////////////////////////////////
    /// PROJECTS
    /////////////////////////////////////////////////////////////////////////////

    // GET ALL PROJECTS ------------------------------------------------------------------------------------------------
    public function getAllProjects(): array
    {
        try {
            $stmt = $this->db->prepare('SELECT Projects.*, CONCAT(projectLeader.firstName, " ", projectLeader.lastName, " (", projectLeader.username, ")") as leaderName, 
                            CONCAT(customer.firstName, " ", customer.lastName, " (", customer.username, ")") as customerName
                            FROM Projects
                            LEFT JOIN Users as projectLeader on projectLeader.userID = Projects.projectLeader
                            LEFT JOIN Users as customer on customer.userID = Projects.customer ORDER BY Projects.startTime DESC;');
            $stmt->execute();
            if ($projects = $stmt->fetchAll(PDO::FETCH_CLASS, "Project")) {
                return $projects;
            } else {
                $this->notifyUser("Projects not found", "Kunne ikke hente prosjekter");
                //return new Project();
                return array();
            }
        } catch (Exception $e) {
            $this->NotifyUser("En feil oppstod, på getAllProjects()", $e->getMessage());
            //return new Project();
            return array();
        }
    }


    // GET PROJECTS OF USER ------------------------------------------------------------------------------------------------
    public function getProjectsOfUser($userId): array
    {
        try {
            $stmt = $this->db->prepare('SELECT Projects.*, CONCAT(projectLeader.firstName, " ", projectLeader.lastName, " (", projectLeader.username, ")") as leaderName, 
                            CONCAT(customer.firstName, " ", customer.lastName, " (", customer.username, ")") as customerName
                            FROM Projects
                            LEFT JOIN Groups ON Groups.projectName = Projects.projectName
                            LEFT JOIN UsersAndGroups ON Groups.groupID = UsersAndGroups.groupID
                            LEFT JOIN Users as projectLeader on projectLeader.userID = Projects.projectLeader
                            LEFT JOIN Users as customer on customer.userID = Projects.customer 
WHERE UsersAndGroups.userID = :userID OR projectLeader = :userID OR customer = :userID GROUP BY ProjectID ORDER BY Projects.startTime DESC;');
            $stmt->bindParam(':userID', $userId, PDO::PARAM_INT);
            $stmt->execute();
            if ($projects = $stmt->fetchAll(PDO::FETCH_CLASS, "Project")) {
                return $projects;
            } else {
                $this->notifyUser("Projects not found", "Kunne ikke hente mine prosjekter");
                //return new Project();
                return array();
            }
        } catch (Exception $e) {
            $this->NotifyUser("En feil oppstod, på getProjectsOfUser()", $e->getMessage());
            //return new Project();
            return array();
        }
    }



    //GET PROJECT ------------------------------------------------------------------------------------------------
    public function getProject(int $projectID)
    {
        try {
            $stmt = $this->db->prepare('SELECT Projects.*, CONCAT(projectLeader.firstName, " ", projectLeader.lastName, " (", projectLeader.username, ")") as leaderName, 
                            CONCAT(customer.firstName, " ", customer.lastName, " (", customer.username, ")") as customerName
                            FROM Projects
                            LEFT JOIN Users as projectLeader on projectLeader.userID = Projects.projectLeader
                            LEFT JOIN Users as customer on customer.userID = Projects.customer WHERE Projects.projectID = :projectID;');
            $stmt->bindParam(':projectID', $projectID, PDO::PARAM_INT, 100);
            $stmt->execute();
            if ($project = $stmt->fetchObject("Project")) {
                return $project;
            } else {
                $this->notifyUser("Ingen prosjekt funnet med dette navnet.", "Kunne ikke hente prosjektet.");
                return null;
            }
        } catch (Exception $e) {
            $this->NotifyUser("En feil oppstod, på getProject()", $e->getMessage());
            return null;
        }
    }


    //GET PROJECT BY NAME ------------------------------------------------------------------------------------------------
    public function getProjectByName(String $projectName)
    {
        try {
            $stmt = $this->db->prepare( 'SELECT Projects.*, CONCAT(projectLeader.firstName, " ", projectLeader.lastName, " (", projectLeader.username, ")") as leaderName, 
                                    CONCAT(customer.firstName, " ", customer.lastName, " (", customer.username, ")") as customerName
                                    FROM Projects
                                    LEFT JOIN Users as projectLeader on projectLeader.userID = Projects.projectLeader
                                    LEFT JOIN Users as customer on customer.userID = Projects.customer WHERE Projects.projectName = :projectName;');
            $stmt->bindParam(':projectName', $projectName, PDO::PARAM_STR, 100);
            $stmt->execute();
            if ($project = $stmt->fetchObject("Project")) {
                return $project;
            } else {
                $this->notifyUser("Ingen prosjekt funnet med dette navnet.", "Kunne ikke hente prosjektet.");
                return null;
            }
        } catch (Exception $e) {
            $this->NotifyUser("En feil oppstod, på getProject()", $e->getMessage());
            return null;
        }
    }


    // ADD PROJECT ------------------------------------------------------------------------------------------------
    public function addProject(): bool
    {
        $isAcceptedByAdmin = $this->session->get('User')->isAdmin() ? 1:0;
        $projectName = $this->request->request->get('projectName');

        //Ungå initsialisering av 01.01.1970 00:00:00 om starttid og slutttid ikke er lagt inn av bruker.
        $dateTime1 = $this->request->request->get('startTime');
        $dateTime2 = $this->request->request->get('finishTime');
        if ($dateTime1 != null) {
            $dateTimeStr1 = date('Y-m-d\TH:i:s', strtotime($dateTime1));
            $startTime = $dateTimeStr1;
        } else {
            $startTime = $dateTime1;
        }

        if ($dateTime2 != null) {
            $dateTimeStr2 = date('Y-m-d\TH:i:s', strtotime($dateTime2));
            $finishTime = $dateTimeStr2;
        } else {
            $finishTime = $dateTime2;
        }
        $status = $this->request->request->get('status');
        $customer = $this->request->request->get('customer');

        try {
            $stmt = $this->db->prepare(
                 "insert into Projects (projectName, startTime, finishTime, status, customer, isAcceptedByAdmin) 
                values (:projectName, :startTime, :finishTime, :status, :customer, :isAcceptedByAdmin);");
            $stmt->bindParam(':projectName', $projectName, PDO::PARAM_STR);
            $stmt->bindParam(':startTime', $startTime, PDO::PARAM_STR);
            $stmt->bindParam(':finishTime', $finishTime, PDO::PARAM_STR);
            $stmt->bindParam(':status', $status, PDO::PARAM_INT);
            $stmt->bindParam(':customer', $customer, PDO::PARAM_INT);
            $stmt->bindParam(':isAcceptedByAdmin', $isAcceptedByAdmin, PDO::PARAM_INT);
            $stmt->execute();
            if ($stmt->rowCount() == 1) {
                $this->notifyUser("Nytt prosjekt ble opprettet", "Fullført!");

                return true;
            } else {
                $this->notifyUser("Feil ved opprettelse av nytt prosjekt");
                return false;
            }
        } catch (Exception $e) {
            $this->notifyUser("Feil ved opprettelse av nytt prosjekt!", $e->getMessage());
            return false;
        }
    }



    //EDIT PROJECT ------------------------------------------------------------------------------------------------
    public function editProject(Project $project): bool
    {
        $projectName = $project->getProjectName();
        $projectLeader = $this->request->request->get('projectLeader');
        $startTime = $this->request->request->get('startTime', $project->getStartTime());
        $finishTime = $this->request->request->get('finishTime', $project->getFinishTime());
        $status = $this->request->request->get('status', $project->getStatus());
        $customer = $this->request->request->get('customer', $project->getCustomer());
        $oldProjectLeader = $project->getProjectLeader();
        try {
            $stmt = $this->db->prepare( "update Projects set projectName = :projectName, projectLeader = :projectLeader, startTime = :startTime, 
                        finishTime = :finishTime, status = :status, customer = :customer 
                        WHERE projectName = :projectName;
                        UPDATE Users SET Users.isProjectLeader = 0
                        WHERE NOT EXISTS
                        (SELECT projectLeader FROM Projects WHERE projectLeader = :oldProjectLeader) AND Users.userID = :oldProjectLeader;
                        UPDATE Users SET Users.isProjectLeader = 1 WHERE Users.userID = :projectLeader;");
            $stmt->bindParam(':projectName', $projectName);
            $stmt->bindParam(':projectLeader', $projectLeader, PDO::PARAM_INT);
            $stmt->bindParam(':startTime', $startTime);
            $stmt->bindParam(':finishTime', $finishTime);
            $stmt->bindParam(':status', $status, PDO::PARAM_INT);
            $stmt->bindParam(':customer', $customer, PDO::PARAM_INT);
            $stmt->bindParam(':oldProjectLeader', $oldProjectLeader, PDO::PARAM_INT);
            if ($stmt->execute()) {
                $stmt->closeCursor();
                $this->notifyUser('Project details changed');
                return true;
            } else {
                $this->notifyUser('Failed to change project details');
                return false;
            }
        } catch (Exception $e) {
            $this->notifyUser("Failed to change project details", $e->getMessage());
            return false;
        }
    }


    //REMOVE MEMBER ------------------------------------------------------------------------------------------------
    public function removeMember(Project $project) : bool
    {
        $userId = $this->request->request->get('projectMember');
        $projectName = $project->getProjectName();
        $projectLeader = $project->getProjectLeader();
        try {
            $stmt = $this->db->prepare( "DELETE FROM UsersAndGroups 
                    WHERE userId = :userID 
                      AND EXISTS (
                          SELECT * FROM Groups WHERE Groups.projectName = :projectName 
                      );
                    UPDATE Projects SET Projects.projectLeader = NULL 
                    WHERE projectName = :projectName AND Projects.projectLeader = :userID;
                    UPDATE Users SET Users.isProjectLeader = 0
                    WHERE NOT EXISTS
                    (SELECT projectLeader FROM Projects WHERE projectLeader = :projectLeader) AND Users.userID = :projectLeader;");
            $stmt->bindParam(':projectName', $projectName, PDO::PARAM_STR);
            $stmt->bindParam(':userID', $userId, PDO::PARAM_INT);
            $stmt->bindParam(':projectLeader', $projectLeader, PDO::PARAM_INT);
            if ($stmt->execute()) {
                $this->notifyUser("Arbeider fjernet fra prosjektet");
                return true;
            } else {
                $this->notifyUser("Feil ved fjerning av arbeider fra prosjektet");
                return false;
            }
        } catch (Exception $e) {
            $this->notifyUser("Arbeider fjernet fra prosjektet", $e->getMessage());
            return false;
        }
    }

    //GET MEMBERS ------------------------------------------------------------------------------------------------
    public function getProjectMembers(string $projectName) : array {
        try {
            $stmt = $this->db->prepare("SELECT DISTINCT Users.* FROM Users
                        LEFT JOIN UsersAndGroups ON Users.userID = UsersAndGroups.userID
                        LEFT JOIN Groups ON UsersAndGroups.groupID = Groups.groupID
                        WHERE Groups.projectName = :projectName;");
            $stmt->bindParam(':projectName', $projectName, PDO::PARAM_STR, 100);
            $stmt->execute();
            if ($members = $stmt->fetchAll(PDO::FETCH_CLASS, 'User')) {
                return $members;
            } else {
                $this->notifyUser("Ingen medlemmer funnet", "getProjectMembers()");
                return array();
            }
        } catch (Exception $e) {
            $this->NotifyUser("En feil oppstod, på getProjectMembers()", $e->getMessage());
            return array();
        }
    }

    //CHECK IF MEMBER OF PROJECT
    public function checkIfMemberOfProject($projectName, $userId) : bool {
        try {
            $stmt = $this->db->prepare('SELECT Projects.*
                            FROM Projects
                            LEFT JOIN Groups ON Groups.projectName = Projects.projectName
                            LEFT JOIN UsersAndGroups ON Groups.groupID = UsersAndGroups.groupID
WHERE (UsersAndGroups.userID = :userID OR projectLeader = :userID OR customer = :userID) AND Projects.projectName = :projectName GROUP BY ProjectID ORDER BY Projects.startTime DESC;');
            $stmt->bindParam(':projectName', $projectName, PDO::PARAM_STR);
            $stmt->bindParam(':userID', $userId, PDO::PARAM_INT);
            $stmt->execute();
            if ($stmt->rowCount() >= 1) {
                return true;
            } else {
                return false;
            }
        }  catch (Exception $e) {
            $this->NotifyUser("En feil oppstod, på checkIfMemberOfProject(projectName, userId)", $e->getMessage());
            return false;
        }
    }



    // ADD GROUP ------------------------------------------------------------------------------------------------
    public function addGroup($projectName): bool //returns boolean value
    {
        $groupName = $this->request->request->get('groupName');
        $isAdmin = $this->request->request->getInt('isAdmin', 0);
        try {
            $stmt = $this->db->prepare("INSERT INTO `Groups` (groupName, isAdmin, projectName)
              VALUES (:groupName, :isAdmin, :projectName);");
            $stmt->bindParam(':groupName', $groupName, PDO::PARAM_STR, 100);
            $stmt->bindParam(':isAdmin', $isAdmin, PDO::PARAM_INT, 100);
            $stmt->bindParam(':projectName', $projectName, PDO::PARAM_STR, 100);
            if ($stmt->execute()) {
                $groupId = $this->db->lastInsertId();
                if ($this->addEmployees($groupId)) {
                    $this->NotifyUser("En gruppe ble lagt til prosjektet");
                    return true;
                } else {
                    return false;
                }
            } else {
                $this->NotifyUser("Feil ved å legge til gruppe");
                return false;
            }
        } catch (PDOException $e) {
            $this->NotifyUser("Feil ved å legge til gruppe", $e->getMessage());
            return false;
        }
    }

    // ADD EMPLOYEES ------------------------------------------------------------------------------------------------
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

    // GET ALL GROUPS ------------------------------------------------------------------------------------------------
    public function getGroups($projectName) : array {
        $groups = array();
        try {
            $stmt = $this->db->prepare("SELECT Groups.*, count(UsersAndGroups.groupID) as nrOfUsers, CONCAT(Users.firstName, ' ', Users.lastName, ' ',' (', Users.username, ') ') as fullName
                        FROM Groups
                        JOIN UsersAndGroups ON Groups.groupID = UsersAndGroups.groupID
                        LEFT JOIN Users ON Groups.groupLeader = Users.userID
                        WHERE Groups.projectName = :projectName GROUP BY Groups.groupID;");
            $stmt->bindParam(':projectName', $projectName, PDO::PARAM_STR);
            if ($stmt->execute()) {
                $groups = $stmt->fetchAll(PDO::FETCH_CLASS, "Group");
                return $groups;
            } else {
                $this->notifyUser("Feil i getGroups()");
                return $groups;
            }
        } catch (Exceptopn $e) {
            $this->notifyUser("Feil i getGroups()", $e->getMessage());
            return $groups;}
    }

    //GET GROUP FROM USERSANDGROUPS ------------------------------------------------------------------------------------
    public function getGroupFromUserAndGroups($projectName) : array {
        $groupsFromUsersAndGroups = array();
        try {
            $stmt = $this->db->prepare("SELECT * FROM UsersAndGroups
                        JOIN `Groups` ON Groups.groupID = UsersAndGroups.groupID
                        WHERE Groups.projectName = :projectName");
            $stmt->bindParam(':projectName', $projectName, PDO::PARAM_STR);
            if ($stmt->execute()) {
                $groupsFromUsersAndGroups = $stmt->fetchAll(PDO::FETCH_CLASS, "Group");
                return $groupsFromUsersAndGroups;
            } else {
                $this->notifyUser("Feil i getGroupFromUserAndGroups");
                return $groupsFromUsersAndGroups;
            }
        } catch (Exceptopn $e) {
            $this->notifyUser("Feil i getGroupFromUserAndGroups", $e->getMessage());
            return $groupsFromUsersAndGroups;}
    }

    // GET LEADER CANDIDATE ------------------------------------------------------------------------------------------------
    public function getLeaderCandidates(String $projectName) : array
    {
        $candidates = array();
        try {
            $stmt = $this->db->prepare("SELECT DISTINCT Users.*
                    FROM Users
                    JOIN UsersAndGroups ON Users.userID = UsersAndGroups.userID
                    JOIN Groups ON UsersAndGroups.groupID = Groups.groupID
                    WHERE Groups.projectName = :projectName
                    AND NOT EXISTS(SELECT Groups.groupLeader FROM Groups WHERE Users.userID = Groups.groupLeader
                    AND Groups.projectName = :projectName) ORDER BY Users.lastName;");
            $stmt->bindParam(':projectName', $projectName, PDO::PARAM_STR);
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

    /*
    public function getAllMembers($projectName) : array


    // REMOVE GROUPS ------------------------------------------------------------------------------------------------
    public function removeGroups(Project $project) : bool
    {
        $members = array();
        try {
            $stmt = $this->db->prepare("SELECT * FROM Users
LEFT JOIN UsersAndGroups as usg on usg.userID = Users.userID
LEFT JOIN Groups as grp on grp.groupID = usg.groupID WHERE grp.projectName = :projectName ORDER BY Users.lastName;");
            $stmt->bindPara(':projectName', $projectName, PDO::PARAM_STR);
            $stmt->execute();
            if ($members = $stmt->fetchAll(PDO::FETCH_CLASS, "User")) {
                return $members;
            } else {
                $this->notifyUser("Brukere ble ikke funnet");
                return $members;
            }
        } catch (Exception $e) {
            $this->NotifyUser("En feil oppstod, på getAllMembers()", $e->getMessage());
            return $members;
        }
        return $members;


    // REMOVE GROUPS ------------------------------------------------------------------------------------------------
    public function removeGroups(Project $project) : bool
    {
        $users = $this->request->request->get('projectMembers');
        $projectName = $project->getProjectName();
        $projectLeader = $project->getProjectLeader();
        try {
            $stmt = $this->db->prepare(query: "DELETE FROM UsersAndProjects
                    WHERE projectName = :projectName AND userId = :userID;
                    UPDATE Projects SET Projects.projectLeader = NULL
                    WHERE projectName = :projectName AND Projects.projectLeader = :userID;
                    UPDATE Users SET Users.isProjectLeader = 0
                    WHERE NOT EXISTS
                    (SELECT projectLeader FROM Projects WHERE projectLeader = :projectLeader) AND Users.userID = :projectLeader;");
            if (is_array($users)) {
                foreach ($users as $userID) {
                    $stmt->bindParam(':projectName', $projectName);
                    $stmt->bindParam(':userID', $userID, PDO::PARAM_INT);
                    $stmt->bindParam(':projectLeader', $projectLeader, PDO::PARAM_INT);
                    $stmt->execute();
                    $stmt->closeCursor();
                }
                return true;
            } else {
                $this->notifyUser("Failed to remove employee", '..........');
                return false;
            }
        } catch (Exception $e) {
            $this->notifyUser("Failed to remove employee", $e->getMessage());
            return false;
        }
    }
}*/


    //DELETE PROJECT ------------------------------------------------------------------------------------------------
    public function deleteProject(String $projectName) : bool
    {
        $projectLeader = $this->request->request->get('projectLeader');
        try {
            $stmt = $this->db->prepare("DELETE FROM Projects WHERE projectName = :projectName;
                                    UPDATE Users SET Users.isProjectLeader = 0
                                    WHERE NOT EXISTS
                                    (SELECT projectLeader FROM Projects WHERE projectLeader = :projectLeader) AND Users.userID = :projectLeader;");
            $stmt->bindParam(':projectName', $projectName, PDO::PARAM_STR);
            $stmt->bindParam(':projectLeader', $projectLeader, PDO::PARAM_INT);
            $stmt->execute();
            if ($stmt->rowCount() >= 1) {
                $this->notifyUser("Project deleted", "Det var ikke noe svar fra databasen");
                return true;
            } else {
                $this->notifyUser("Failed to delete group!", "Det var ikke noe svar fra databasen");
                return false;
            }
        } catch (Exception $e) {
            $this->notifyUser("Failed to delete project!", $e->getMessage());
            return false;
        }
    }



    // ADD PHASE ------------------------------------------------------------------------------------------------
    public function addPhase (Project $project) : bool
    {
        $projectName = $project->getProjectName();
        $phaseName = $this->request->request->get('phaseName');
        $startTime = $this->request->request->get('startTime');
        $finishTime = $this->request->request->get('finishTime');
        $status = $this->request->request->getInt('status', 0);

        if (strtotime($startTime) < strtotime($project->getStartTime())) {
            $startTime = $project->getStartTime();
        }
        if (strtotime($finishTime) > strtotime($project->getFinishTime())) {
            $finishTime = $project->getFinishTime();
        }
        try{
            $sth = $this->db->prepare("insert into Phases (phaseName, projectName, startTime, finishTime, status) 
                values (:phaseName, :projectName, :startTime, :finishTime, :status);");
            $sth->bindParam(':phaseName', $phaseName, PDO::PARAM_STR);
            $sth->bindParam(':projectName', $projectName, PDO::PARAM_STR);
            $sth->bindParam(':startTime',  $startTime, PDO::PARAM_STR);
            $sth->bindParam(':finishTime', $finishTime, PDO::PARAM_STR);
            $sth->bindParam(':status', $status, PDO::PARAM_INT);
            $sth->execute();
            if ($sth->rowCount() == 1) {
                $this->notifyUser("Ny fase ble lagt til");
                return true;
            } else {
                $this->notifyUser("Feil ved registrering av fase!");
                return false;
            }
        } catch (Exception $e) {
            $this->notifyUser("Feil ved registrering av fase!", $e->getMessage());
            return false;
        }
    }


    // EDIT PHASE ------------------------------------------------------------------------------------------------
    public function editPhase (Phase $phase, Project $project) : bool
    {
        $phaseId = $phase->getPhaseID();
        $phaseName = $this->request->request->get('phaseName', $phase->getPhaseName());
        $startTime = $this->request->request->get('startTime', $phase->getStartTime());
        $finishTime = $this->request->request->get('finishTime', $phase->getFinishTime());
        $status = $this->request->request->getInt('status', $phase->getStatus());
        if (strtotime($startTime) < strtotime($project->getStartTime())) {
            $startTime = $project->getStartTime();
        }
        if (strtotime($finishTime) > strtotime($project->getFinishTime())) {
            $finishTime = $project->getFinishTime();
        }
        try{
            $sth = $this->db->prepare("update Phases set phaseName = :phaseName, startTime = :startTime, 
                  finishTime = :finishTime, status = :status where phaseID = :phaseID;");
            $sth->bindParam(':phaseName', $phaseName, PDO::PARAM_STR);
            $sth->bindParam(':startTime',  $startTime, PDO::PARAM_STR);
            $sth->bindParam(':finishTime', $finishTime, PDO::PARAM_STR);
            $sth->bindParam(':status', $status, PDO::PARAM_INT);
            $sth->bindParam(":phaseID", $phaseId, PDO::PARAM_INT);
            $sth->execute();
            if ($sth->rowCount() == 1) {
                $this->notifyUser("Fase ble endret");
                return true;
            } else {
                $this->notifyUser("Feil ved endring av fase!");
                return false;
            }
        } catch (Exception $e) {
            $this->notifyUser("Feil ved endring av fase!", $e->getMessage());
            return false;
        }
    }

    // DELETE PHASE ------------------------------------------------------------------------------------------------
    public function deletePhase ($phaseId) : bool
    {
        try{
            $sth = $this->db->prepare("delete from Phases where phaseID = :phaseID;");
            $sth->bindParam(":phaseID", $phaseId, PDO::PARAM_INT);
            $sth->execute();
            if ($sth->rowCount() == 1) {
                $this->notifyUser("Fase ble slettet");
                return true;
            } else {
                $this->notifyUser("Feil ved sletting av fase!");
                return false;
            }
        } catch (Exception $e) {
            $this->notifyUser("Feil ved sletting av fase!", $e->getMessage());
            return false;
        }
    }

    // GET PHASE -----------------------------------------------------------------------------------------------
    public function getPhase($phaseId)
    {
        try {
            $stmt = $this->db->prepare( 'SELECT * FROM Phases WHERE phaseID = :phaseID;');
            $stmt->bindParam(':phaseID', $phaseId, PDO::PARAM_INT, 100);
            $stmt->execute();
            if ($phase = $stmt->fetchObject("Phase")) {
                return $phase;
            } else {
                $this->notifyUser("Ingen prosjekt funnet med dette navnet.", "Kunne ikke hente prosjektet.");
                return null;
            }
        } catch (Exception $e) {
            $this->NotifyUser("En feil oppstod, på getProject()", $e->getMessage());
            return null;
        }
    }

    // GET ALL PHASES ------------------------------------------------------------------------------------------------
    public function getAllPhases($projectName) : array
    {
        $phases = array();
        try{
            $sth = $this->db->prepare("select * from Phases where projectName = :projectName;");
            $sth->bindParam(":projectName", $projectName, PDO::PARAM_STR);
            $sth->execute();
            if ($phases = $sth->fetchAll(PDO::FETCH_CLASS, "Phase")) {
                return $phases;
            } else {
                $this->notifyUser("Feil ved henting av faser!");
                return $phases;
            }
        } catch (Exception $e) {
            $this->notifyUser("Feil ved henting av faser!", $e->getMessage());
            return $phases;
        }
    }

    // VERIFY PROJECT BY ADMIN ------------------------------------------------------------------------------------------------
    public function verifyProjectByAdmin(int $projectID) : bool {
        try {
            $sth = $this->db->prepare("update Projects set isAcceptedByAdmin = 1 where projectID = :projectID");
            $sth->bindParam(':projectID', $projectID, PDO::PARAM_STR);
            $sth->execute();
            if($sth->rowCount() == 1) {
                $this->notifyUser("Prosjekt godkjent av admin");
                return true;
            } else {
                $this->notifyUser("Feil ved godkjenning av prosjekt");
                return false;
            }
        } catch (Exception $e) {
            $this->notifyUser("Feil ved godkjenning av prosjekt", $e->getMessage());
            return false;
        }
    }


}