<?php


class ReportGenerator
{
    private $db;
    private $request;
    private $session;

    //CONSTRUCTOR//
    function __construct(PDO $db, \Symfony\Component\HttpFoundation\Request $request, \Symfony\Component\HttpFoundation\Session\Session $session)
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

    // GET ALL PROJECTS FOR REPORT
    public function getAllProjectsForReport(): array
    {
        try {
            $stmt = $this->db->prepare('SELECT Projects.*, CASE WHEN Tasks.estimatedTime IS NULL THEN 0 ELSE SUM(Tasks.estimatedTime) END AS sumEstimate, CASE WHEN Tasks.estimatedTime IS NULL THEN 0 ELSE SUM(CASE WHEN Tasks.status = 3 THEN Tasks.estimatedTime ELSE 0 END) END AS sumEstimateDone,
       CASE WHEN Tasks.timeSpent IS NULL THEN 0 ELSE SUM(Tasks.timeSpent) END AS sumTimeSpent FROM Projects 
LEFT JOIN Tasks on Projects.projectName = Tasks.projectName 
WHERE Tasks.hasSubtask = 1 or Tasks.hasSubtask IS NULL GROUP BY Projects.projectName ORDER BY Projects.projectName;');
            $stmt->execute();
            if ($projects = $stmt->fetchAll(PDO::FETCH_CLASS, "Project")) {
                return $projects;
            } else {
                $this->notifyUser("Projects not found", "Kunne ikke hente prosjekter");
                //return new Project();
                return array();
            }
        } catch (Exception $e) {
            $this->NotifyUser("En feil oppstod, på getallProjectsForReport()", $e->getMessage());
            //return new Project();
            return array();
        }
    }


    //GET PROJECT
    public function getDataOnProjectForReport(int $projectID)
    {
        try {
            $stmt = $this->db->prepare(query: 'SELECT Projects.*, CONCAT(projectLeader.firstName, " ", projectLeader.lastName, " (", projectLeader.username, ")") as leaderName, 
CONCAT(customer.firstName, " ", customer.lastName, " (", customer.username, ")") as customerName,
CASE WHEN Tasks.estimatedTime IS NULL THEN 0 ELSE SUM(Tasks.estimatedTime) END AS sumEstimate, CASE WHEN Tasks.estimatedTime IS NULL THEN 0 ELSE SUM(CASE WHEN Tasks.status = 3 THEN Tasks.estimatedTime ELSE 0 END) END AS sumEstimateDone, 
       CASE WHEN Tasks.timeSpent IS NULL THEN 0 ELSE SUM(Tasks.timeSpent) END AS sumTimeSpent
FROM Projects
LEFT JOIN Users as projectLeader on projectLeader.userID = Projects.projectLeader
LEFT JOIN Users as customer on customer.userID = Projects.customer 
LEFT JOIN Tasks on Projects.projectName = Tasks.projectName 
WHERE Projects.projectID = :projectID AND (Tasks.hasSubtask = 1 OR Tasks.hasSubtask IS NULL) GROUP BY Projects.projectName;');
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

    // GET PROGRESS DATA
    public function getProgressData($projectName): array {
        $datas = array();
        try{
            $sth = $this->db->prepare("SELECT * FROM ProgressTable WHERE projectName = :projectName ORDER BY registerDate;");
            $sth->bindParam(":projectName", $projectName, PDO::PARAM_STR);
            $sth->execute();
            if ($datas = $sth->fetchAll()) {
                $this->notifyUser("Progress data hentent ");
                return $datas;
            } else {
                $this->notifyUser("Feil ved henting av progress data!");
                return $datas;
            }
        } catch (Exception $e) {
            $this->notifyUser("Feil ved henting av progress data!", $e->getMessage());
            return $datas;
        }
    }


    // GET ALL PROJECTS FOR REPORT
    public function getAllGroupsForReport(): array
    {
        try {
            $stmt = $this->db->prepare('SELECT Projects.*, CASE WHEN Tasks.estimatedTime IS NULL THEN 0 ELSE SUM(Tasks.estimatedTime) END AS sumEstimate, CASE WHEN Tasks.estimatedTime IS NULL THEN 0 ELSE SUM(CASE WHEN Tasks.status = 3 THEN Tasks.estimatedTime ELSE 0 END) END AS sumEstimateDone,
       CASE WHEN Tasks.timeSpent IS NULL THEN 0 ELSE SUM(Tasks.timeSpent) END AS sumTimeSpent FROM Projects 
LEFT JOIN Tasks on Projects.projectName = Tasks.projectName 
WHERE Tasks.hasSubtask = 1 or Tasks.hasSubtask IS NULL GROUP BY Projects.projectName ORDER BY Projects.projectName;');
            $stmt->execute();
            if ($projects = $stmt->fetchAll(PDO::FETCH_CLASS, "Project")) {
                return $projects;
            } else {
                $this->notifyUser("Projects not found", "Kunne ikke hente prosjekter");
                //return new Project();
                return array();
            }
        } catch (Exception $e) {
            $this->NotifyUser("En feil oppstod, på getallProjectsForReport()", $e->getMessage());
            //return new Project();
            return array();
        }
    }


    //GET PROJECT
    public function getGroupForReport(int $groupID)
    {
        try {
            $stmt = $this->db->prepare(query: 'SELECT Projects.*, CONCAT(projectLeader.firstName, " ", projectLeader.lastName, " (", projectLeader.username, ")") as leaderName, 
CONCAT(customer.firstName, " ", customer.lastName, " (", customer.username, ")") as customerName,
CASE WHEN Tasks.estimatedTime IS NULL THEN 0 ELSE SUM(Tasks.estimatedTime) END AS sumEstimate, CASE WHEN Tasks.estimatedTime IS NULL THEN 0 ELSE SUM(CASE WHEN Tasks.status = 3 THEN Tasks.estimatedTime ELSE 0 END) END AS sumEstimateDone, 
       CASE WHEN Tasks.timeSpent IS NULL THEN 0 ELSE SUM(Tasks.timeSpent) END AS sumTimeSpent
FROM Projects
LEFT JOIN Users as projectLeader on projectLeader.userID = Projects.projectLeader
LEFT JOIN Users as customer on customer.userID = Projects.customer 
LEFT JOIN Tasks on Projects.projectName = Tasks.projectName 
WHERE Projects.projectID = :projectID AND (Tasks.hasSubtask = 1 OR Tasks.hasSubtask IS NULL) GROUP BY Projects.projectName;');
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




}