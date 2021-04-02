<?php


require_once "includes.php";

define('FILENAME_TAG', 'image');

$hourManager = new HourManager($db, $request, $session);
$userManager = new UserManager($db, $request, $session);
$taskManager = new TaskManager($db, $request, $session);


if ($user) {
    $userID = $user->getUserId($user);
    $tasks = $taskManager->getAllTasks();
    $hours = $hourManager->getAllHoursForUser($userID);
    $hourId = $request->query->getInt('hourID');
    $hourWithTask = $hourManager->getAllHoursForUserWithTask($userID);
    $hour = $hourManager->getHour($hourId);

    if ($request->request->has('edit_comment_hour') && XsrfProtection::verifyMac("Edit Comment")) {
        if ($hourManager->editComment($hourId)) {
            header("Location: ".$request->server->get('REQUEST_URI'));
            exit();
        } else {
            header("Location: ".$request->server->get('REQUEST_URI')."&failedtaddphase=!");
            exit();
        }
    }
    else {

        echo $twig->render('employee_dashboard.twig',
            array('hours' => $hours, 'hour' => $hour, 'hourWithTask' => $hourWithTask,'HourManager' => $hourManager,
                'UserID' => $userID, 'session' => $session, 'user' => $user, 'tasks' => $tasks));

    }
} else {
    header("location: login.php");
    exit();
}


