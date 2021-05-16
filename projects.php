<?php

require_once "includes.php";


if (!is_null($user)) {
    $ProjectManager = new ProjectManager($db, $request, $session);
    $projects = $ProjectManager->getAllProjects();

    $projectsOfUser = $ProjectManager->getProjectsOfUser($user->getUserID());

    $userManager = new UserManager($db, $request, $session);
    $users = $userManager->getAllUsers("lastName");

    echo $twig->render('projects.twig',
        array('projects' => $projects, 'projectManager' => $ProjectManager, 'session' => $session,
            'user' => $user,  'users' => $users, 'projectsOfUser' => $projectsOfUser));
}
else {
    header("location: index.php");
    exit();
}

