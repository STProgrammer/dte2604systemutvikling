<?php


require_once "includes.php";

$hourManager = new HourManager($db, $request, $session);
$userManager = new UserManager($db, $request, $session);
$taskManager = new TaskManager($db, $request, $session);


if ($user) {
    $userID = $user->getUserId($user);
    $tasks = $taskManager->getAllTasks();

    $hourId = $request->query->getInt('hourID');
    $hour = $hourManager->getHour($hourId);
    $hours = $hourManager->getHours(whoWorked: $userID);

    $hourID = $hourManager->activeTimeregForUser($userID);

    }
    if ($request->request->has('edit_comment_hour') && XsrfProtection::verifyMac("Edit Comment")) {
        if ($hourManager->editComment($hour)) {
            header("Location: ".$requestUri);
            exit();
        } else {
            header("Location: ".$requestUri."&failedtocomment=1");
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

        echo $twig->render('employee_dashboard.twig',
            array('hours' => $hours, 'hour' => $hour,'hourManager' => $hourManager,
                'UserID' => $userID, 'session' => $session, 'user' => $user, 'tasks' => $tasks,
                'TaskManager'=> $taskManager, 'hourID'=> $hourID));
    } else {
    header("location: login.php");
    exit();
}


