<?php

require_once "includes.php";
define('FILENAME_TAG', 'image');

    if (!isset($db)) {
        echo $twig->render('error.twig', array('msg' => 'No database connected!'));
    }
    if (empty($twig)) {
        echo $twig->render('error.twig', array('msg' => 'Twig not working!'));
    }
$user = $session->get('User');

if ($user) {
    $ProjectManager = new ProjectManager($db, $request, $session);
    $projects = $ProjectManager->getAllProjects();

    //TODO Antall medlemmer
    //$members = $ProjectManager->getProjectMembers($request->query->get('projectName'));
    //$members = $ProjectManager->getProjectMembers($request->query->get('projectName'));
    //$membersCount = count($members);

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

