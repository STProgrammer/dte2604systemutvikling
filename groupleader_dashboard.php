<?php



require_once "includes.php";

define('FILENAME_TAG', 'image');

    if ($user) {
        $hourManager = new HourManager($db, $request, $session);
        $userManager = new UserManager($db, $request, $session);
        $taskManager = new TaskManager($db, $request, $session);

        $userID = $user->getUserId($user);
        $tasks = $taskManager->getAllTasks();

        $hours = $hourManager->getHours(whoWorked: $userID);
        $hoursAll = $hourManager->getAllHours();
        $hourId = $request->query->getInt('hourID');
        $hour = $hourManager->getHour($hourId);
        $hourID = $hourManager->activeTimeregForUser($userID);
    }
    if ($request->request->has('edit_comment_hour') && XsrfProtection::verifyMac("Edit Comment")) {
        if ($hourManager->editComment($hour)) {
            header("Location: ".$request->server->get('REQUEST_URI'));
            exit();
        } else {
            header("Location: ".$request->server->get('REQUEST_URI')."&failedtaddphase=!");
            exit();
        }
    }
    //START time
    if ($request->request->has('register_time')) {
        if ($hourManager->registerTimeForUser($userID)) {
            header("Location: groupleader_dashboard.php?registeredhour=1");
            exit();
        } else {
            header("Location: ?failedtoregistrerhour=1");
            exit();
        }
    }
    //STOP time
    if ($request->request->has('stop_time')) {
        if ($hourManager->activeTimeregForUser($userID)) {
            if ($hourManager->stopTimeForUser($hourID)) {
                header("Location: groupleader_dashboard.php?stopregisteredhour=1");
                exit();
            }
        } else {
            header("Location: ?failedtostopregistrerhour=1");
            exit();
        }
    }
    if ($user) {

        echo $twig->render('groupleader_dashboard.twig',
            array('session' => $session, 'request' => $request,
                'user' => $user,
                'hours' => $hours, 'hour' => $hour, 'hoursAll' => $hoursAll,
                'hourManager' => $hourManager,
                'UserID' => $userID, 'tasks' => $tasks,
                'taskManager'=> $taskManager,
                'hourID' => $hourID));

    } else {
        header("location: login.php");
        exit();
    }