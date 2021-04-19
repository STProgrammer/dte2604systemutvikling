<?php

require_once "includes.php";
define('FILENAME_TAG', 'image');

if ($user->isProjectLeader() or $user->isGroupleader()) {
    $ProjectManager = new ProjectManager($db, $request, $session);
    $projects = $ProjectManager->getAllProjects();

    $userManager = new UserManager($db, $request, $session);

    echo $twig->render('report_print.twig',
        array('projects' => $projects, 'ProjectManager' => $ProjectManager, 'session' => $session,
            'user' => $user));
}
else {
    header("location:index.php");
    exit();
}