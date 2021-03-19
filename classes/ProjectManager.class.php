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

    // GET ALL PROJECTS
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

    // ADD PROJECT
    public function addProject (Project $project) : bool
    {
        $projectName = $this->request->request->get('projectName');
        $projectLeader = $this->request->request->get('projectLeader');

        $dateTime1 = $this->request->request->get('startTime');
        $dateTimeStr1 = date('Y-m-d\TH:i:s', strtotime($dateTime1));
        $startTime = $dateTimeStr1;

        $dateTime2 = $this->request->request->get('startTime');
        $dateTimeStr2 = date('Y-m-d\TH:i:s', strtotime($dateTime2));
        $finishTime = $dateTimeStr2;

        $status = $this->request->request->get('status');
        $customer = $this->request->request->get('customer');

        try{
            $stmt = $this->db->prepare(
                "insert into Projects (projectName, projectLeader, startTime, finishTime, status, customer) 
                values (:projectName, :projectLeader, :startTime, :finishTime, :status, :customer);");
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
                $this->notifyUser("Failed to register user!", "Ikke fullført");
                return false;
            }
        } catch (Exception $e) {
            $this->notifyUser("Failed to register user!", $e->getMessage());
            return false;
        }
    }
    // END - ADD PROJECT

    /////////////////////////////////////////////////////////////////////////////
    /// ERROR
    /////////////////////////////////////////////////////////////////////////////

    private function NotifyUser($strHeader, $strMessage) {
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