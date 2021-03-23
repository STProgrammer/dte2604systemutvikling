<?php

require_once "includes.php";
define('FILENAME_TAG', 'image');

    if (!isset($db)) {
        echo $twig->render('error.twig', array('msg' => 'No database connected!'));
    }
    if (empty($twig)) {
        echo $twig->render('error.twig', array('msg' => 'Twig not working!'));
    }

    $ProjectManager = new ProjectManager($db, $request, $session);
    $projects = $ProjectManager->getAllProjects();
    //$members = $ProjectManager->getProjectMembers($request->query->get('projectName'));
    //$membersCount = count($members);

    $user = $session->get('User');

    $userManager = new UserManager($db, $request, $session);
    $users = $userManager->getAllUsers("lastName");

    echo $twig->render('projects.twig',
        array('projects' => $projects, 'ProjectManager' => $ProjectManager, 'session' => $session,
            'User' => $user,  'users' => $users, ));



//    $session->getFlashBag->clear(); //sletter flashbagmeldinger

