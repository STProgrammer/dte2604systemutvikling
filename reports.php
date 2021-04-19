<?php

require_once "includes.php";
define('FILENAME_TAG', 'image');
if ($user) {
    $hourManager = new HourManager($db, $request, $session);
    $userManager = new UserManager($db, $request, $session);
    $taskManager = new TaskManager($db, $request, $session);

    if ($user->isProjectleader() or $user->isGroupLeader()) {
        $ProjectManager = new ProjectManager($db, $request, $session);
        $projects = $ProjectManager->getAllProjects();

        $userManager = new UserManager($db, $request, $session);
        $users = $userManager->getAllUsers("lastName");

        echo $twig->render('reports.twig',
            array('projects' => $projects, 'ProjectManager' => $ProjectManager, 'session' => $session,
                'user' => $user, 'users' => $users));


    }
} else {
    header("location: index.php");
    exit();
}
