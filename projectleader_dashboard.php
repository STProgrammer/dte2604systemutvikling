<?php

require_once "includes.php";

define('FILENAME_TAG', 'image');

$hourManager = new HourManager($db, $request, $session);
$userManager = new UserManager($db, $request, $session);
$taskManager = new TaskManager($db, $request, $session);


if ($user) {



    $userID = $user->getUserId($user);
    $tasks = $taskManager->getAllTasks();

    $tasksWork = $taskManager->getTasksOfUser($userID);

    $hours = $hourManager->getAllHours(); //kun denne brukerens kommentarer
    $hourId = $request->query->getInt('hourID');
//    $hourWithTask = $hourManager->getAllHoursForUserWithTask($userID);
    $hour = $hourManager->getHour($hourId);
    $hourID = $hourManager->activeTimeregForUser($userID);
    if ($request->request->has('edit_comment_hour') && XsrfProtection::verifyMac("Edit Comment")) {
        if ($hourManager->editComment($hour)) {
            header("Location: " . $request->server->get('REQUEST_URI'));
            exit();
        } else {
            header("Location: " . $request->server->get('REQUEST_URI') );
            exit();
        }
    } //START time
    else if ($request->request->has('register_time')) {
        $task = $taskManager->getTask($request->request->getInt('taskID', 0));
        if ($hourManager->registerTimeForUser($userID, $task)) {
            header("Location: " . $requestUri);
            exit();
        } else {
            header("Location: " . $requestUri);
            exit();
        }
    }//STOP time
    else if ($request->request->has('stop_time')) {
        if ($hourManager->activeTimeregForUser($userID)) {
            //$stopTime = $hourManager->stopTimeForUser($hourID);
            if ($hourManager->stopTimeForUser($hourID)) {
                header("Location: " . $requestUri );
                exit();
            }
        } else {
            header("Location: " . $requestUri );
            exit();
        }
    } else {
        echo $twig->render('projectleader_dashboard.twig',
            array('hours' => $hours, 'hour' => $hour, 'hourManager' => $hourManager,
                'UserID' => $userID, 'session' => $session, 'user' => $user, 'tasks' => $tasks,
                'TaskManager' => $taskManager, 'hourID' => $hourID, 'tasksWork' => $tasksWork));
    }
}else {
    header("location: login.php");
    exit();
}