<?php


require_once "includes.php";

define('FILENAME_TAG', 'image');

$user = $session->get('User');


$hourManager = new HourManager($db, $request, $session);
$userManager = new UserManager($db, $request, $session);
$taskManager = new TaskManager($db, $request, $session);
$tasks = $taskManager->getAllTasks();

if ($user) {
    $userID = $user->getUserId($user);
    $hour = $hourManager->getAllHoursForUser($userID);
    $hourWithTask = $hourManager->getAllHoursForUserWithTask($userID);

    echo $twig->render('timeregistrations.twig',
        array('Hour' => $hour, 'hourWithTask' => $hourWithTask,'HourManager' => $hourManager, 'UserID' => $userID, 'session' => $session, 'user' => $user, 'tasks' => $tasks));

} else {
    header("location: login.php");
    exit();
}
