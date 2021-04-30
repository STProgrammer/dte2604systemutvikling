<?php

require_once "includes.php";



if ($user) {
    $hourManager = new HourManager($db, $request, $session);
    $userManager = new UserManager($db, $request, $session);
    $taskManager = new TaskManager($db, $request, $session);
    $projectManager = new ProjectManager($db, $request, $session);
    $reportGenerator = new ReportGenerator($db, $request, $session);

    $userID = $user->getUserId($user);
    $users = $userManager->getAllUsers('dateRegistered');
    $statistics = $reportGenerator->getAllUserStatistics();

    $hours = $hourManager->getAllHours();
    $categories = $taskManager->getCategories();
    $tasks = $taskManager->getAllTasks();

    //SUM OF ALL HOURS
    $sum = strtotime('00:00:00');
    $sum2 = 0;
    foreach ($statistics as $statistic){
        $sum1 = strtotime($statistic->sumThisMonth) - $sum;
        $sum2 = $sum2 + $sum1;
    }
    $sum3=$sum+$sum2;
    $sumTime =  date("H:i:s",$sum3);

    //PAYMENT
    $sumPayment = $sumTime * 1500;


    if ($user->isAdmin()) {
        try {
            echo $twig->render('admin_dashboard.twig',
                array( 'session' => $session, 'request' => $request,
                    'user' => $user, 'users' => $users,
                    'hours' => $hours, 'hourManager' => $hourManager, 'sumTime' => $sumTime, 'sumPayment' => $sumPayment,
                    'tasks' => $tasks));
        } catch (LoaderError | RuntimeError | SyntaxError $e) {
            echo $e->getMessage();
        }
    }
    elseif ($user->isProjectLeader()) {
        header("location: projectleader_dashboard.php");

    }
    elseif ($user->isCustomer()) {
        header("location: customer_dashboard.php");
    }
    elseif ($user->isGroupLeader()) { //TEAMLEDER
        header("location: groupleader_dashboard.php");

    }
    elseif ($user->isUser()) { //BRUKER TEMP and WORKER
        header("location: employee_dashboard.php");
    }
} else {
    header("location: login.php");
    exit();
}

?>