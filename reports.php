<?php

require_once "includes.php";
define('FILENAME_TAG', 'image');
if ($user) {
    $hourManager = new HourManager($db, $request, $session);
    $userManager = new UserManager($db, $request, $session);
    $taskManager = new TaskManager($db, $request, $session);
    $projectManager = new ProjectManager($db, $request, $session);
    $timeSpentSum = null;
    $estimertTidSum = null;

    if ($user->isAdmin() or $user->isProjectleader()) {
        $projects = $projectManager->getAllProjects();

        $userManager = new UserManager($db, $request, $session);
        $users = $userManager->getAllUsers("lastName");
        $tasks = $taskManager->getAllTasks();

        echo $twig->render('reports.twig',
            array('session' => $session, 'user' => $user, 'users' => $users,
                'projects' => $projects, 'ProjectManager' => $projectManager,
                'tasks' => $tasks,
                'timeSpentSum' => $timeSpentSum, 'estimertTidSum' => $estimertTidSum));

    } elseif ($user->isGroupLeader()) {
        $projects = $projectManager->getAllProjects();

        $userManager = new UserManager($db, $request, $session);
        $users = $userManager->getAllUsers("lastName");

        echo $twig->render('reports.twig',
            array('projects' => $projects, 'ProjectManager' => $projectManager, 'session' => $session,
                'user' => $user, 'users' => $users));
    } elseif ($user->isEmployee()) {
        $projects = $projectManager->getAllProjects();

        $userManager = new UserManager($db, $request, $session);
        $users = $userManager->getAllUsers("lastName");

        echo $twig->render('reports.twig',
            array('projects' => $projects, 'ProjectManager' => $projectManager, 'session' => $session,
                'user' => $user, 'users' => $users));
    } elseif ($user->isCustomer()) {
        $projects = $projectManager->getAllProjects();

        $userManager = new UserManager($db, $request, $session);
        $users = $userManager->getAllUsers("lastName");

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
