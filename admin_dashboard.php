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


    //PAYMENT ---------------------------
    $statistics = $reportGenerator->getAllUserStatistics();
    $calculateDay = $hourManager->calculateDay($statistics);
    $calculateMonth = $hourManager->calculateMonth($statistics);

    $sumTimeToday = $calculateDay[1];
    $sumPaymentToday = $calculateDay[0];
    $sumTimeMonth = $calculateMonth[1];
    $sumPaymentMonth = $calculateMonth[0];

    //TWIG ----------------------------------------------------
    $array = array('session' => $session, 'request' => $request,
        'user' => $user, 'users' => $users,
        'hours' => $hours, 'hourManager' => $hourManager,
        'sumTimeMonth' => $sumTimeMonth, 'sumPaymentMonth' => $sumPaymentMonth,
        'sumTimeToday' => $sumTimeToday, 'sumPaymentToday' => $sumPaymentToday,
        'tasks' => $tasks);

    if (!empty($twig)) {
        try {
            echo $twig->render('admin_dashboard.twig', $array);
        } catch (LoaderError | RuntimeError | SyntaxError $e) {
            echo $e->getMessage();
        }
    }
}



