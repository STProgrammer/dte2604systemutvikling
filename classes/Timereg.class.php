<?php


class Timereg {
    private PDO $db;

    //__________CONSTRUCTOR_______________//
    public function __construct(PDO $db) {
        $this->db = $db;
    }

    //___________PROJECT__________________//
    public function getAllProjects(): array {
        $projects = array();
        $stmt = $this->db->prepare("SELECT * FROM Projects ORDER BY `Start time` DESC");
        try {
            $stmt->execute();
            $projects = $stmt->fetchAll(PDO::FETCH_CLASS, 'Project');
        } catch (Exception $e) {
            $this->NotifyUser("En feil oppstod, på getAllProjects()", $e->getMessage());
            print $e->getMessage() . PHP_EOL;
        }
        return $projects;
    }

    //___________ERROR--------------------//
    private function NotifyUser($strHeader, $strMessage) {
        echo "<h3> $strHeader</h3>";
        echo "<p>$strMessage</p>";
    }

}