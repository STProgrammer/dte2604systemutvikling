<?php


require_once "includes.php";

define('FILENAME_TAG', 'image');

$user = $session->get('User');


$hourManager = new HourManager($db, $request, $session);
$userManager = new UserManager($db, $request, $session);

if ($user) {
    $userID = $user->getUserId($user);
    $hour = $hourManager->getAllHoursForUser($userID);

    echo $twig->render('timeregistrations.twig',
        array('Hour' => $hour, 'HourManager' => $hourManager, 'UserID' => $userID, 'session' => $session, 'user' => $user));
} else {
    header("location: login.php");
    exit();
}
