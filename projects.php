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
    $user = $session->get('User');

    if (isset($_POST['addProject']) && !empty($_POST['addProject'])) {
        $project = new Bruker();
        $project->setProjectName(filter_input(INPUT_POST, 'ProjectName', FILTER_SANITIZE_STRING));
        $project->setProjectName(filter_input(INPUT_POST, 'ProjectName', FILTER_SANITIZE_EMAIL));
        $project->setStartTime(filter_input(INPUT_POST, 'StartTime', FILTER_SANITIZE_EMAIL));
        $project->setFinishTime(filter_input(INPUT_POST, 'FinishTime', FILTER_SANITIZE_STRING));
        $project->setStatus(filter_input(INPUT_POST, 'Status', FILTER_SANITIZE_STRING));
        $project->setCustomer(filter_input(INPUT_POST, 'Customer', FILTER_SANITIZE_STRING));
        $project->setIsAcceptedByAdmin(filter_input(INPUT_POST, 'IsAcceptedByAdmin', FILTER_SANITIZE_STRING));

        //lage en unik id
        $project->settAktiveringsId($aktiveringsid = md5(uniqid(rand(), 1)));
        $ProjectManager->leggInnNyBruker($project);

        //hent epost fra input
        $epost = $_POST['epost'];
        $brukernavn = $_POST['brukernavn'];

        echo $twig->render('projects.twig', array('projects' => $projects,
            'ProjectManager' => $ProjectManager, 'session' => $session, 'User' => $user ));
    }else{
        echo $twig->render('projects.twig', array('projects' => $projects,
            'ProjectManager' => $ProjectManager, 'session' => $session, 'User' => $user ));
    }

