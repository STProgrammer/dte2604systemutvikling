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

    $hours = $hourManager->getAllHours();
    $categories = $taskManager->getCategories();
    $tasks = $taskManager->getAllTasks();

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

    $array = array( 'session' => $session, 'request' => $request,
        'user' => $user, 'users' => $users,
        'hours' => $hours, 'hourManager' => $hourManager, 'sumTime' => $sumTime, 'sumPayment' => $sumPayment,
        'tasks' => $tasks);


}


if (!empty($twig)) {
    try {
        echo $twig->render('admin_dashboard.twig', $array);
    } catch (LoaderError | RuntimeError | SyntaxError $e) {
        echo $e->getMessage();
    }
}
