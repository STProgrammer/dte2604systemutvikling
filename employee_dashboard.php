<?php


require_once "includes.php";

define('FILENAME_TAG', 'image');

$user = $session->get('User');


$hourManager = new HourManager($db, $request, $session);
$userManager = new UserManager($db, $request, $session);

if ($user) {
    $userID = $userManager->getUserID($request->query->getInt('userID'));
    $hour = $hourManager->getAllHoursForUser($userID, "startTime");

    echo $twig->render('employee_dashboard.twig',
        array('Hour' => $hour, 'UserID' => $userID, 'session' => $session, 'User' => $user));
} else {
    header("location: login.php");
    exit();
}



