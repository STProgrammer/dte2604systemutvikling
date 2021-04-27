<?php


require_once "includes.php";

$hourManager = new HourManager($db, $request, $session);
$userManager = new UserManager($db, $request, $session);
$taskManager = new TaskManager($db, $request, $session);


if ($user) {
    $userID = $user->getUserId($user);

    $tasks = $taskManager->getTasksOfUser($userID);

    $hourId = $request->query->getInt('hourID');
    $hour = $hourManager->getHour($hourId);
    $hours = $hourManager->getHours(null, $userID);

    $hourID = $hourManager->activeTimeregForUser($userID);

    if ($request->request->has('edit_comment_hour') && XsrfProtection::verifyMac("Edit Comment")) {
        if ($hourManager->editComment($hour)) {
            header("Location: " . $requestUri);
            exit();
        } else {
            header("Location: " . $requestUri );
            exit();
        }
    } //START time
    else if ($request->request->has('register_time')) {
        $task = $taskManager->getTask($request->request->getInt('taskID', 0));
        if ($hourManager->registerTimeForUser($userID, $task)) {
            header("Location: " . $requestUri  );
            exit();
        } else {
            header("Location: " . $requestUri );
            exit();
        }
    } //STOP time
    else if ($request->request->has('stop_time')) {
        if ($hourManager->activeTimeregForUser($userID)) {
            $hour = $hourManager->getHour($hourID[0]);
            $task = $taskManager->getTask($hour->getTaskId());
            //$stopTime = $hourManager->stopTimeForUser($hourID);
            if ($hourManager->stopTimeForUser($hour, $task)) {
                header("Location: " . $requestUri );
                exit();
            }
        } else {
            header("Location: " . $requestUri );
            exit();
        }
    }  else {

        echo $twig->render('employee_dashboard.twig',
            array('hours' => $hours, 'hour' => $hour, 'hourManager' => $hourManager,
                'UserID' => $userID, 'session' => $session, 'user' => $user, 'tasks' => $tasks,
                'TaskManager' => $taskManager, 'hourID' => $hourID));
    }
}else {
    header("location: login.php");
    exit();
}


