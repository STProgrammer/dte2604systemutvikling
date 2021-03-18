<?php


use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

class ProjectManager {
    private $db;
    private $request;
    private $session;

    //CONSTRUCTOR//
    function __construct(PDO $db, Request $request, Session $session) {
        $this->db = $db;
        $this->request = $request;
        $this->session = $session;
    }

    /////////////////////////////////////////////////////////////////////////////
    /// PROJECTS
    /////////////////////////////////////////////////////////////////////////////

    public function getAllProjects() : array {
        try {
            $stmt = $this->db->prepare("SELECT * FROM Projects ORDER BY `startTime` DESC");
            $stmt->execute();
            if( $projects = $stmt->fetchAll(PDO::FETCH_CLASS, "Project")) {
                return $projects;
            }
            else {
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

    /////////////////////////////////////////////////////////////////////////////
    /// ERROR
    /////////////////////////////////////////////////////////////////////////////

    private function NotifyUser($strHeader, $strMessage) {
        $this->session->getFlashBag()->clear();
        $this->session->getFlashBag()->add('header', $strHeader);
        $this->session->getFlashBag()->add('message', $strMessage);
    }

    public function newProject() {}

    public function editProject(Project $project) {}

    public function deleteProject() {}

    public function getProject(String $projectName) {}

    public function addGroup(Group $group) {}

    public function acceptByAdmin() {}

    public function addEmployee(User $user, Project $project) {}

    public function getEmployees(Project $project) {}

    public function addCustomer(User $user, Project $project) {}

    public function getCustomers(Project $project) {}

    public function assignLeader(User $leader) {}

    public function changeStatus() {}

}