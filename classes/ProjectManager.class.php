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

    // GET ALL PROJECTS
    public function getAllProjects(): array
    {
        try {
            $stmt = $this->db->prepare(query: "SELECT * FROM Projects ORDER BY `startTime` DESC");
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
            print $e->getMessage() . PHP_EOL;
            //return new Project();
            return array();
        }
    }

    //GET PROJECT
    public function getProject(string $projectName)
    {
        try {
            $stmt = $this->db->prepare(query: "SELECT projectName, projectLeader, startTime, finishTime, Projects.status, customer 
                                                FROM Projects LEFT JOIN Users ON Projects.projectLeader=Users.UserID 
                                                    WHERE Projects.projectName = :projectName;");
            $stmt->bindParam(':projectName', $projectName, PDO::PARAM_STR, 100);
            $stmt->execute();
            if ($project = $stmt->fetchObject("Project")) {
                return $project;
            } else {
                $this->notifyUser("Ingen prosjekt funnet med dette navnet.", "Kunne ikke hente prosjektet.");
                return new Project();
            }
        } catch (Exception $e) {
            $this->NotifyUser("En feil oppstod, på getProject()", $e->getMessage());
            return new Project();
        }
    }



    // ADD PROJECT
    public function addProject(): bool
    {
        //TODO disse variablene nedenfor bør hentes fra objektet som sendes med i kallet. Halil
        $projectName = $this->request->request->get('projectName');
        $projectLeader = $this->request->request->get('projectLeader');

        //Ungå initsialisering av 01.01.1970 00:00:00 om starttid og slutttid ikke er lagt inn av bruker.
        $dateTime1 = $this->request->request->get('startTime');
        if ($dateTime1 != null) {
            $dateTimeStr1 = date('Y-m-d\TH:i:s', strtotime($dateTime1));
            $startTime = $dateTimeStr1;
        } else {
            $startTime = $dateTime1;
        }
        $dateTime2 = $this->request->request->get('startTime');
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
                query: "insert into Projects (projectName, projectLeader, startTime, finishTime, status, customer) 
                values (:projectName, :projectLeader, :startTime, :finishTime, :status, :customer);
                update Users set isProjectLeader = 0 where userID = :projectLeader;
                INSERT IGNORE INTO UsersAndProjects (userID, projectName) VALUES (:projectLeader, :projectName);");
            $stmt->bindParam(':projectName', $projectName);
            $stmt->bindParam(':projectLeader', $projectLeader, PDO::PARAM_INT);
            $stmt->bindParam(':startTime', $startTime);
            $stmt->bindParam(':finishTime', $finishTime);
            $stmt->bindParam(':status', $status, PDO::PARAM_INT);
            $stmt->bindParam(':customer', $customer, PDO::PARAM_INT);
            $stmt->execute();
            if ($stmt->rowCount() == 1) {
                $this->notifyUser("Nytt prosjekt ble registrert", "Fullført!");

                return true;
            } else {
                $this->notifyUser("Failed to register project!", "Ikke fullført");
                return false;
            }
        } catch (Exception $e) {
            $this->notifyUser("Failed to register project!", $e->getMessage());
            return false;
        }
    }

    //EDIT PROJECT
    public function editProject(Project $project): bool
    {
        $projectName = $this->request->request->get('projectName', $project->getProjectName());
        $projectLeader = $this->request->request->getInt('projectLeader', $project->getProjectLeader());
        $startTime = $this->request->request->get('startTime', $project->getStartTime());
        $finishTime = $this->request->request->get('finishTime', $project->getFinishTime());
        $status = $this->request->request->getInt('status', $project->getStatus());
        $customer = $this->request->request->getInt('customer', $project->getCustomer());
        $oldProjectLeader = $project->getProjectLeader();
        try {
            $stmt = $this->db->prepare(query: "update Projects set projectName = :projectName, projectLeader = :projectLeader, startTime = :startTime, 
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

    public function addEmployees($projectName)
    {
        $users = $this->request->request->get('projectMembers');
        try {
            $stmt = $this->db->prepare(query: "INSERT IGNORE INTO UsersAndProjects (userID, projectName) VALUES (:userID, :projectName);");
            if (is_array($users)) {
                foreach ($users as $userID) {
                    $stmt->bindParam(':userID', $userID, PDO::PARAM_INT);
                    $stmt->bindParam(':projectName', $projectName);
                    $stmt->execute();
                }
                $this->notifyUser("Medlemmer ble lagt til", '..........');
            } else {
                $this->notifyUser("Kunne ikke legge til medlemmer", '..........');
                return false;
            }
        } catch (Exception $e) {
            $this->notifyUser("Kunne ikke legge til medlemmer", $e->getMessage());
            return false;
        }
        return true;
    }


    public function removeEmployees(Project $project)
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
            } else {
                $this->notifyUser("Failed to remove employee", '..........');
                return false;
            }
        } catch (Exception $e) {
            $this->notifyUser("Failed to remove employee", $e->getMessage());
            return false;
        }
        return true;
    }

    //GET MEMBERS
    public function getProjectMembers(string $projectName) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM Users WHERE EXISTS(SELECT UsersAndProjects.userID FROM UsersAndProjects WHERE UsersAndProjects.projectName = :projectName AND Users.userID = UsersAndProjects.userID);");
            $stmt->bindParam(':projectName', $projectName, PDO::PARAM_STR, 100);
            $stmt->execute();
            if ($members = $stmt->fetchAll(PDO::FETCH_CLASS, 'User')) {
                return $members;
            } else {
                $this->notifyUser("Ingen medlemmer funnet", "Kunne ikke hente medlemmer av prosjektet");
                return array();
            }
        } catch (Exception $e) {
            $this->NotifyUser("En feil oppstod, på getProjectMembers()", $e->getMessage());
            return array();
        }
    }


    public function addGroups($projectName)
    {
        $groups = $this->request->request->get('groups');
        try {
            $stmt = $this->db->prepare(query: "INSERT IGNORE INTO GroupsAndProjects (groupID, projectName) VALUES (:groupID, :projectName);");
            if (is_array($groups)) {
                foreach ($groups as $groupID) {
                    $stmt->bindParam(':groupID', $groupID, PDO::PARAM_INT);
                    $stmt->bindParam(':projectName', $projectName, PDO::PARAM_STR);
                    $stmt->execute();
                }
                $this->notifyUser("Grupper ble lagt til", '..........');
            } else {
                $this->notifyUser("Kunne ikke legge til grupper", '..........');
                return false;
            }
        } catch (Exception $e) {
            $this->notifyUser("Kunne ikke legge til grupper", $e->getMessage());
            return false;
        }
        return true;
    }


    public function removeGroups(Project $project)
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
            } else {
                $this->notifyUser("Failed to remove employee", '..........');
                return false;
            }
        } catch (Exception $e) {
            $this->notifyUser("Failed to remove employee", $e->getMessage());
            return false;
        }
        return true;
    }



    //DELETE PROJECT
    public function deleteProject(string $projectName)
    {
        $projectLeader = $this->request->request->get('projectLeader');
        try {
            $stmt = $this->db->prepare(query: "DELETE FROM Projects WHERE projectName = :projectName; 
                                    DELETE FROM UsersAndProjects WHERE projectName = :projectName;
                                    UPDATE Users SET Users.isProjectLeader = 0
                                    WHERE NOT EXISTS
                                    (SELECT projectLeader FROM Projects WHERE projectLeader = :projectLeader) AND Users.userID = :projectLeader;");
            $stmt->bindParam(':projectName', $projectName);
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



    // ADD PHASE
    public function addPhase ($projectName) : bool
    {
        $phaseName = $this->request->request->get('phaseName');
        $startTime = $this->request->request->get('startTime');
        $finishTime = $this->request->request->get('finishTime');
        try{
            $sth = $this->db->prepare("insert into Phases (phaseName, projectName, startTime, finishTime, status) 
                values (:phaseName, :projectName, :startTime, :finishTime, 0);");
            $sth->bindParam(':phaseName', $phaseName, PDO::PARAM_STR);
            $sth->bindParam(':projectName', $projectName, PDO::PARAM_STR);
            $sth->bindParam(':startTime',  $startTime, PDO::PARAM_STR);
            $sth->bindParam(':finishTime', $finishTime, PDO::PARAM_STR);
            $sth->execute();
            if ($sth->rowCount() == 1) {
                $this->notifyUser("Ny fase ble lagt til");
                return true;
            } else {
                $this->notifyUser("Feil ve registrering av fase!");
                return false;
            }
        } catch (Exception $e) {
            $this->notifyUser("Feil ved registrering av fase!", $e->getMessage());
            return false;
        }
    }
    // END ADD PHASE


    // EDIT PHASE
    public function editPhase (Phase $phase) : bool
    {
        $phaseId = $phase->getPhaseID();
        $phaseName = $this->request->request->get('phaseName', $phase->getPhaseName());
        $startTime = $this->request->request->get('startTime', $phase->getStartTime());
        $finishTime = $this->request->request->get('finishTime', $phase->getFinishTime());
        $status = $this->request->request->getInt('status', $phase->getStatus());
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
                $this->notifyUser("Feil ve endring av fase!");
                return false;
            }
        } catch (Exception $e) {
            $this->notifyUser("Feil ved endring av fase!", $e->getMessage());
            return false;
        }
    }
    // END EDIT PHASE



    // DELETE PHASE
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
                $this->notifyUser("Feil ve sletting av fase!");
                return false;
            }
        } catch (Exception $e) {
            $this->notifyUser("Feil ved sletting av fase!", $e->getMessage());
            return false;
        }
    }
    // END DELETE PHASE


    // GET PHASE WITH TASKS
    /*public function getPhase ($phaseId) : array
    {
        try{
            $sth = $this->db->prepare("select * from Phases where projcetName = :projectName;");
            $sth->bindParam(":projectName", $projectName, PDO::PARAM_INT);
            $sth->execute();
            if ($sth->rowCount() == 1) {
                return true;
            } else {
                $this->notifyUser("Feil ve henting av faser!");
                return false;
            }
        } catch (Exception $e) {
            $this->notifyUser("Feil ved henting av faser!", $e->getMessage());
            return false;
        }
    }*/
    // END GET PHASE WITH TASKS



    // GET ALL PHASES
    public function getAllPhases ($projectName) : array
    {
        try{
            $sth = $this->db->prepare("select * from Phases where projcetName = :projectName;");
            $sth->bindParam(":projectName", $projectName, PDO::PARAM_INT);
            $sth->execute();
            if ($sth->rowCount() == 1) {
                return true;
            } else {
                $this->notifyUser("Feil ve henting av faser!");
                return false;
            }
        } catch (Exception $e) {
            $this->notifyUser("Feil ved henting av faser!", $e->getMessage());
            return false;
        }
    }
    // END GET ALL PHASES








    //TODO
    public function addGroup(Group $group)
    {

    }

    public function verifyProjectByAdmin($projectName) : bool {
        if($this->session->get('User')->isAdmin()) {
            try {
                $sth = $this->dbase->prepare("update Projects set isAcceptedByAdmin = 1 where projectName = :projectName");
                $sth->bindParam(':projectName', $projectName, PDO::PARAM_STR);
                $sth->execute();
                if($sth->rowCount() == 1) {
                    $this->notifyUser("Project verified by admin", "");
                    return true;
                } else {
                    $this->notifyUser("Failed to verify project", "");
                    return false;
                }
            } catch (Exception $e) {
                $this->notifyUser("Failed to verify project", $e->getMessage());
                return false;
            }
        } else {return false; }
    }

    //TODO
    public function getEmployees(Project $project)
    {
    }

    //TODO
    public function addCustomer(User $user, Project $project)
    {
    }

    //TODO
    public function getCustomers(Project $project)
    {
    }
}