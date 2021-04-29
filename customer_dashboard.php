<?php


require_once "includes.php";

$hourManager = new HourManager($db, $request, $session);
$userManager = new UserManager($db, $request, $session);
$taskManager = new TaskManager($db, $request, $session);
$projectManager = new ProjectManager($db, $request, $session);
$reportGenerator = new ReportGenerator($db, $request, $session);

$projects = array();

if ($user->isCustomer()) {
    $tasks = $taskManager->getAllTasks();

    $projectsOfUser = $projectManager->getProjectsOfUser($user->getUserID());

    $projects = $reportGenerator->getProjectsForUserForReport($user->getUserID());

    echo $twig->render('customer_dashboard.twig',
        array('hourManager' => $hourManager,
            'session' => $session, 'user' => $user,
            'projectsOfUser' => $projectsOfUser, 'projects' => $projects,
            'reportGenerator' => $reportGenerator,
            'tasks' => $tasks,
            'TaskManager'=> $taskManager,));
}else {
    header("location: login.php");
    exit();
}

