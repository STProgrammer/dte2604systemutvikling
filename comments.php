<?php

require_once "includes.php";
define('FILENAME_TAG', 'image');

if (!isset($db)) {
    echo $twig->render('error.twig', array('msg' => 'No database connected!'));
}
if (empty($twig)) {
    echo $twig->render('error.twig', array('msg' => 'Twig not working!'));
}

$commentsManager = new CommentsManager($db, $request, $session);
$hours = $commentsManager->getAllHours();

$user = $session->get('User');

$userManager = new UserManager($db, $request, $session);
$users = $userManager->getAllUsers("lastName");

$TaskManager = new TaskManager($db, $request, $session);
$tasks = $TaskManager->getAllTasks();

echo $twig->render('comments.twig',
    array('session' => $session, 'hours' => $hours, 'user' => $user, 'users' => $users, 'tasks' => $tasks));






/*
 //----------------------------------------------KOMMENTAR-------------------------------------------------------------
    public function visKommentarer(int $dokumentId): array {
        $kommentarer = array();
        $stmt = $this->db->prepare("SELECT * FROM Kommentar WHERE FK_Dokument_id = :dokumentId");
        $stmt->bindparam(':dokumentId', $dokumentId, PDO::PARAM_INT);
        try {
            $stmt->execute();
        } catch (InvalidArgumentException $e) {
            $this->NotifyUser("En feil oppstod, på visKommentarer()", $e->getMessage());
            print $e->getMessage() . PHP_EOL;
        }
        $kommentarer = $stmt->fetchAll(PDO::FETCH_CLASS, "Kommentar");
        return $kommentarer;
    }
    public function leggTilKommentar(int $brukerId, int $dokumentId, string $kommentar) {
        $stmt = $this->db->prepare("INSERT INTO Kommentar (FK_Bruker_id, FK_Dokument_id, innhold, dato)
                                            VALUES (:brukerId, :dokumentId, :kommentar, :dato)");
        $stmt->bindValue(":brukerId", $brukerId, PDO::PARAM_INT);
        $stmt->bindValue(":dokumentId", $dokumentId, PDO::PARAM_INT);
        $stmt->bindValue(":kommentar", $kommentar, PDO::PARAM_STR);
        $stmt->bindValue(":dato", date('Y-m-d H:i:s'), PDO::PARAM_STR);
        try {
            $stmt->execute();
        } catch (InvalidArgumentException $e) {
            $this->NotifyUser("En feil oppstod, på leggTilKommentar()", $e->getMessage());
        }
    }
    public function slettKommentar(int $kommentarId) {
        $stmt = $this->db->prepare("DELETE FROM Kommentar WHERE Kommentar_id = :kommentarId");
        $stmt->bindValue(':kommentarId', $kommentarId, PDO::PARAM_INT);
        try {
            $stmt->execute();
        } catch (InvalidArgumentException $e) {
            $this->NotifyUser("En feil oppstod, på slettKommentar()", $e->getMessage());
            print $e->getMessage() . PHP_EOL;
        }
    }
 */