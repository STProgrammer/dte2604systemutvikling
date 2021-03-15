<?php


require_once "includes.php";
define('FILENAME_TAG', 'image');

    if (isset($db)) {
        $ProjectManager = new ProjectManager($db, $request, $session);
    }else echo $twig->render('error.twig', array('msg' => 'No database connected!'));

    $projects = $ProjectManager->getAllProjects();
    //

    try {
        if (!empty($twig)) {

            echo $twig->render('projects.twig', array('projects' => $projects, 'Timereg' => $Timereg));
        }
    } catch (LoaderError | RuntimeError | SyntaxError $e) {
    }



