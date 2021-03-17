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

    if (isset($_POST['leggTilProsjekt']) && !empty($_POST['leggTilProsjekt'])) {
        $project = new Bruker();
        $project->setProjectName(filter_input(INPUT_POST, 'brukernavn', FILTER_SANITIZE_STRING));
        $project->setProjectLeader(filter_input(INPUT_POST, 'epost', FILTER_SANITIZE_EMAIL));
        $project->setStartTime(filter_input(INPUT_POST, 'epost2', FILTER_SANITIZE_EMAIL));
        $project->setFinishTime(filter_input(INPUT_POST, 'passord', FILTER_SANITIZE_STRING));
        $project->setStatus(filter_input(INPUT_POST, 'passord2', FILTER_SANITIZE_STRING));
        $project->setCustomer(filter_input(INPUT_POST, 'passord', FILTER_SANITIZE_STRING));
        $project->setIsAcceptedByAdmin(filter_input(INPUT_POST, 'passord2', FILTER_SANITIZE_STRING));

        //lage en unik id
        $project->settAktiveringsId($aktiveringsid = md5(uniqid(rand(), 1)));
        $ProjectManager->leggInnNyBruker($project);

        //hent epost fra input
        $epost = $_POST['epost'];
        $brukernavn = $_POST['brukernavn'];

        echo $twig->render('projects.twig', array('projects' => $projects,
            'ProjectManager' => $ProjectManager, 'session' => $session, 'User' => $user ));
    }

