<?php


require_once "includes.php";

define('FILENAME_TAG', 'image');

if (!isset($db)) {
    echo $twig->render('error.twig', array('msg' => 'No database connected!'));
}
if (empty($twig)) {
    echo $twig->render('error.twig', array('msg' => 'Twig not working!'));
}

$user = $session->get('User');
$userID = $session->get('UserID');

$HourManager = new HourManager($db, $request, $session);
$hours = $HourManager->getAllHoursForUser($userID);

echo $twig->render('employee_dashboard.twig',
    array('Hours' => $hours, 'UserID' => $userID, 'session' => $session, 'User' => $user ));



