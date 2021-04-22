<?php

require_once "includes.php";

if ($user) {
    $hourManager = new HourManager($db, $request, $session);
    $userManager = new UserManager($db, $request, $session);
    $taskManager = new TaskManager($db, $request, $session);
    $projectManager = new ProjectManager($db, $request, $session);
    $timeSpentSum = null;
    $estimertTidSum = null;
    $projects = $projectManager->getAllProjectsForReport();
    $userManager = new UserManager($db, $request, $session);
    $users = $userManager->getAllUsers("lastName");

    if ($user->isAdmin() or $user->isProjectleader()) {

        $tasks = $taskManager->getAllTasks();

        echo $twig->render('reports.twig',
            array('session' => $session, 'user' => $user, 'users' => $users,
                'projects' => $projects, 'ProjectManager' => $projectManager,
                'tasks' => $tasks,
                'timeSpentSum' => $timeSpentSum, 'estimertTidSum' => $estimertTidSum));

    } elseif ($user->isGroupLeader()) {

        echo $twig->render('reports.twig',
            array('projects' => $projects, 'ProjectManager' => $projectManager, 'session' => $session,
                'user' => $user, 'users' => $users));
    } elseif ($user->isEmployee()) {

        echo $twig->render('reports.twig',
            array('projects' => $projects, 'ProjectManager' => $projectManager, 'session' => $session,
                'user' => $user, 'users' => $users));
    } elseif ($user->isCustomer()) {

        echo $twig->render('reports.twig',
            array('projects' => $projects, 'ProjectManager' => $projectManager, 'session' => $session,
                'user' => $user, 'users' => $users));
    } else {
        header("location: index.php");
        exit();
    }
} else {
    header("location: index.php");
    exit();
}
