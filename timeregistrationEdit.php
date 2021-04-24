<?php


require_once "includes.php";


$hourManager = new HourManager($db, $request, $session);
$userManager = new UserManager($db, $request, $session);
$taskManager = new TaskManager($db, $request, $session);

$hourID = $request->query->getInt('hourID', 0);
$hour = $hourManager->getHour($hourID);

if (!is_null($user) and !is_null($hour)) {
    $userID = $user->getUserId($user);

    $tasks = $taskManager->getAllTasks(); //trenger vi den?

    $hours = $hourManager->getHours(whoWorked: $userID);

    $task = $taskManager->getTask($hour->getTaskID());

    if ($request->request->has('edit_comment_hour') && XsrfProtection::verifyMac("Edit Comment")) {
        if ($hourManager->editComment($hourID)) {
            header("Location: ".$requestUri);
            exit();
        } else {
            header("Location: ".$requestUri."&failedtocomment=1");
            exit();
        }
    }

    elseif ($request->request->has('edit_commentBoss_hour') && XsrfProtection::verifyMac("Edit Comment Boss")) {
        if ($hourManager->editCommentBoss($hourID)) {
            header("Location: " . $requestUri );
            exit();
        } else {
            header("Location: " . $requestUri . "&failedtocommentbyboss=1");
            exit();
        }
    }

    ## Endrer timeregistreringen
    elseif ($request->request->has('edit_timereg') && XsrfProtection::verifyMac("Edit Timereg")) {
        $hourManager->duplicateToLog($hour);
        $startTime = $request->request->get('startTime');
        $endTime = $request->request->get('endTime');
        if ($hourManager->changeTimeForUser($hourID, $startTime, $endTime, $task)) {
            header("Location: " . $requestUri );
            exit();
        } else {
            header("Location: " . $requestUri . "&failededithour=1");
            exit();
        }
    }

        ## deaktiverer timeregistreringen
    elseif ($request->request->has('edit_deactivate') && XsrfProtection::verifyMac("Edit Timereg")) {
        $hourManager->duplicateToLog($hour);
        if ($hourManager->deleteTimeForUser($hourID, $task)) {
            header("Location: ".$requestUri."&edithour=1");
            exit();
        } else {
            header("Location: ".$requestUri."&failededithour=1");
            exit();
        }
    }

    else {
        echo $twig->render('timeregistrationsEdit.twig',
                array('hour' => $hour, 'hours' => $hours, 'HourManager' => $hourManager, 'UserID' => $userID, 'session' => $session,
                    'user' => $user, 'tasks' => $tasks, 'hourID' => $hourID));
    }

} else {
    header("location: login.php");
    exit();
}