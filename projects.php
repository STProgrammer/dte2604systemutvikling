<?php

require_once "includes.php";
define('FILENAME_TAG', 'image');

if ($user) {
    $ProjectManager = new ProjectManager($db, $request, $session);
    $projects = $ProjectManager->getAllProjects();

    $userManager = new UserManager($db, $request, $session);
    $users = $userManager->getAllUsers("lastName");

    echo $twig->render('projects.twig',
        array('projects' => $projects, 'ProjectManager' => $ProjectManager, 'session' => $session,
            'user' => $user,  'users' => $users));
}
else {
    header("location: index.php");
    exit();
}

