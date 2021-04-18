<?php

require_once "includes.php";

define('FILENAME_TAG', 'image');

$hourManager = new HourManager($db, $request, $session);
$userManager = new UserManager($db, $request, $session);
$taskManager = new TaskManager($db, $request, $session);


if ($user) {
    $userID = $user->getUserId($user);
    $tasks = $taskManager->getAllTasks();

    $hours = $hourManager->getAllHours(); //kun denne brukerens kommentarer
    $hourId = $request->query->getInt('hourID');
//    $hourWithTask = $hourManager->getAllHoursForUserWithTask($userID);
    $hour = $hourManager->getHour($hourId);
    $hourID = $hourManager->activeTimeregForUser($userID);
}
if ($request->request->has('edit_comment_hour') && XsrfProtection::verifyMac("Edit Comment")) {
    if ($hourManager->editComment($hour)) {
        header("Location: " . $request->server->get('REQUEST_URI'));
        exit();
    } else {
        header("Location: " . $request->server->get('REQUEST_URI') . "&failedtaddphase=!");
        exit();
    }
}
//START time
if ($request->request->has('register_time')) {
    if ($hourManager->registerTimeForUser($userID)) {
        header("Location: ".$requestUri."&registeredhour=1");
        exit();
    } else {
        header("Location: ".$requestUri."&failedtoregistrerhour=1");
        exit();
    }
}
//STOP time
if ($request->request->has('stop_time')) {
    if ($hourManager->activeTimeregForUser($userID)) {
        //$stopTime = $hourManager->stopTimeForUser($hourID);
        if ($hourManager->stopTimeForUser($hourID)) {
            header("Location: ".$requestUri."&stopregisteredhour=1");
            exit();
        }
    } else {
        header("Location: ".$requestUri."&failedtostopregistrerhour=1");
        exit();
    }
}
if ($user) {

    echo $twig->render('projectleader_dashboard.twig',
        array('hours' => $hours, 'hour' => $hour, 'hourManager' => $hourManager,
            'UserID' => $userID, 'session' => $session, 'user' => $user, 'tasks' => $tasks,
            'TaskManager' => $taskManager, 'hourID' => $hourID));

} else {
    header("location: login.php");
    exit();
}