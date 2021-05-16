<?php



require_once "includes.php";

if (!is_null($user)) {
        $hourManager = new HourManager($db, $request, $session);
        $userManager = new UserManager($db, $request, $session);
        $taskManager = new TaskManager($db, $request, $session);

        $userID = $user->getUserId($user);
        $tasks = $taskManager->getAllTasks();

        $tasksWork = $taskManager->getTasksOfUser($userID);
        $taskCategories = $taskManager->getCategories();

        $hours = $hourManager->getHours(null, $userID);
        $hoursAll = $hourManager->getAllHours();
        $hourId = $request->query->getInt('hourID');

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
    }
    else {
        echo $twig->render('groupleader_dashboard.twig',
            array('session' => $session, 'request' => $request,
                'user' => $user,
                'hours' => $hours,  'hoursAll' => $hoursAll,
                'hourManager' => $hourManager,
                'UserID' => $userID, 'tasks' => $tasks,
                'taskManager'=> $taskManager,
                'hourID' => $hourID,
                'tasksWork' => $tasksWork, 'taskCategories' => $taskCategories));
    }
} else {
    header("location: login.php");
    exit();
}