<?php


require_once "includes.php";

define('FILENAME_TAG', 'image');

$user = $session->get('User');


$hourManager = new HourManager($db, $request, $session);
$userManager = new UserManager($db, $request, $session);
$taskManager = new TaskManager($db, $request, $session);
$tasks = $taskManager->getAllTasks();


if ($user) {
    $hourID = $request->query->get('hourID');
    $userID = $user->getUserId($user);
    $hours = $hourManager->getAllHoursForUser($userID);
    $hour = $hourManager->getHour($hourID);

    echo $twig->render('timeregistrationsEdit.twig',
        array('Hour' => $hour, 'Hours' => $hours, 'HourManager' => $hourManager, 'UserID' => $userID, 'session' => $session,
            'user' => $user, 'tasks' => $tasks, 'hourID' => $hourID));

} else {
    header("location: login.php");
    exit();
}
