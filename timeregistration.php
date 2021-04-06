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
    $hourWithTask = $hourManager->getAllHoursForUserWithTask($userID);
    $hour = $hourManager->getHour($userID);
    $hourss = $hourManager->getHours(projectName: 'Noe prosjekt');

    if ($request->request->has('edit_comment_hour') && XsrfProtection::verifyMac("Edit Comment")) {
        if ($hourManager->editComment($hour)) {
            header("Location: ".$request->server->get('REQUEST_URI'));
            exit();
        } else {
            header("Location: ".$request->server->get('REQUEST_URI')."&failedtaddphase=!");
            exit();
        }
    }
    else {
        echo $twig->render('timeregistrations.twig',
            array('hours' => $hours, 'hour' => $hour, 'hourWithTask' => $hourWithTask,'HourManager' => $hourManager,
                'userID' => $userID, 'session' => $session, 'user' => $user, 'tasks' => $tasks));

    }
} else {
    header("location: login.php");
    exit();
}
