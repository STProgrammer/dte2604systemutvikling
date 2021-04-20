<?php



require_once "includes.php";

if ($user) {
        $hourManager = new HourManager($db, $request, $session);
        $userManager = new UserManager($db, $request, $session);
        $taskManager = new TaskManager($db, $request, $session);

        $userID = $user->getUserId($user);
        $tasks = $taskManager->getAllTasks();

        $tasksWork = $taskManager->getTasksOfUser($userID);

        $hours = $hourManager->getHours(whoWorked: $userID);
        $hoursAll = $hourManager->getAllHours();
        $hourId = $request->query->getInt('hourID');
        $hour = $hourManager->getHour($hourId);
        $hourID = $hourManager->activeTimeregForUser($userID);
    if ($request->request->has('edit_comment_hour') && XsrfProtection::verifyMac("Edit Comment")) {
        if ($hourManager->editComment($hour)) {
            header("Location: ".$requestUri);
            exit();
        } else {
            header("Location: ".$requestUri);
            exit();
        }
    }
    //START time
    else if ($request->request->has('register_time')) {
        $task = $taskManager->getTask($request->request->getInt('taskID', 0));
        if ($hourManager->registerTimeForUser($userID, $task)) {
            header("Location: " . $requestUri  );
            exit();
        } else {
            header("Location: " . $requestUri );
            exit();
        }
    }
    //STOP time
    else if ($request->request->has('stop_time')) {
        if ($hourManager->activeTimeregForUser($userID)) {
            if ($hourManager->stopTimeForUser($hourID)) {
                header("Location: ".$requestUri);
                exit();
            } else {
                header("Location: ".$requestUri);
                exit();
            }
        } else {
            header("Location: ".$requestUri);
            exit();
        }
    }
    else {
        echo $twig->render('groupleader_dashboard.twig',
            array('session' => $session, 'request' => $request,
                'user' => $user,
                'hours' => $hours, 'hour' => $hour, 'hoursAll' => $hoursAll,
                'hourManager' => $hourManager,
                'UserID' => $userID, 'tasks' => $tasks,
                'taskManager'=> $taskManager,
                'hourID' => $hourID,
                'tasksWork' => $tasksWork));
    }
} else {
    header("location: login.php");
    exit();
}