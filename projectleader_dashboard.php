<?php

require_once "includes.php";

if ($user) {
    $hourManager = new HourManager($db, $request, $session);
    $userManager = new UserManager($db, $request, $session);
    $taskManager = new TaskManager($db, $request, $session);
    $reportGenerator = new ReportGenerator($db, $request, $session);

    $userID = $user->getUserId($user);
    $tasks = $taskManager->getAllTasks();

    $tasksWork = $taskManager->getTasksOfUser($userID);
    $taskCategories = $taskManager->getCategories();

    $hours = $hourManager->getHours(null, $userID, null, null, null,
        null, null,  null,  null,
        null, false, 1, null,
        "startTime", null, $limit = 5, null );
    $hourId = $request->query->getInt('hourID');
    $hourID = $hourManager->activeTimeregForUser($userID);

    //PAYMENT ---------------------------
    $statistics = $reportGenerator->getAllUserStatistics();
    $calculateDay = $hourManager->calculateDay($statistics);
    $calculateMonth = $hourManager->calculateMonth($statistics);

    $sumTimeToday = $calculateDay[1];
    $sumPaymentToday = $calculateDay[0];
    $sumTimeMonth = $calculateMonth[1];
    $sumPaymentMonth = $calculateMonth[0];

    //SUM OF ALL HOURS
    $statistics = $reportGenerator->getAllUserStatistics();
    $sum = strtotime('00:00:00');
    $sum2 = 0;
    foreach ($statistics as $statistic){
        $sum1 = strtotime($statistic->sumThisMonth) - $sum;
        $sum2 = $sum2 + $sum1;
    }
    $sum3=$sum+$sum2;
    $sumTime =  date("H:i:s",$sum3);

    //PAYMENT
    $sumPayment = intval($sumTime) * 1500;

    if ($request->request->has('edit_comment_hour') && XsrfProtection::verifyMac("Edit Comment")) {
        if ($hourManager->editComment($hour)) {
            header("Location: " . $request->server->get('REQUEST_URI'));
            exit();
        } else {
            header("Location: " . $request->server->get('REQUEST_URI') );
            exit();
        }
    }
    //START time
    else if ($request->request->has('register_time')) {
        $task = $taskManager->getTask($request->request->getInt('taskID', 0));
        if ($hourManager->registerTimeForUser($userID, $task)) {
            header("Location: " . $requestUri);
            exit();
        } else {
            header("Location: " . $requestUri);
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
    } else {
        echo $twig->render('projectleader_dashboard.twig',
            array('hours' => $hours, 'hour' => $hour, 'hourManager' => $hourManager,

                'sumTimeMonth' => $sumTimeMonth, 'sumPaymentMonth' => $sumPaymentMonth,
                'sumTimeToday' => $sumTimeToday, 'sumPaymentToday' => $sumPaymentToday,

                'UserID' => $userID, 'session' => $session, 'user' => $user,

                'tasks' => $tasks, 'taskCategories' => $taskCategories,
                'TaskManager' => $taskManager, 'hourID' => $hourID, 'tasksWork' => $tasksWork));
    }
}else {
    header("location: login.php");
    exit();
}