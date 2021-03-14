<?php


use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

class Timereg {
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

    public function getAllProjects() :Project {
        try {
            $stmt = $this->db->prepare("SELECT * FROM Projects ORDER BY `Start time` DESC");
            $stmt->execute();
            if( $projects = $stmt->fetchObject('Project')) {
                return $projects;
            }
            else {
                $this->notifyUser("Project not found", "");
                return new Project();
            }
        } catch (Exception $e) {
            $this->NotifyUser("En feil oppstod, på getAllProjects()", $e->getMessage());
            print $e->getMessage() . PHP_EOL;
            return new Project();
        }
    }

    /////////////////////////////////////////////////////////////////////////////
    /// ERROR
    /////////////////////////////////////////////////////////////////////////////

    private function NotifyUser($strHeader, $strMessage) {
        echo "<h3> $strHeader</h3>";
        echo "<p>$strMessage</p>";
    }

}