<?php


require_once "includes.php";
define('FILENAME_TAG', 'image');

    if (isset($db)) {
        $ProjectManager = new ProjectManager($db, $request, $session);
    }else echo $twig->render('error.twig', array('msg' => 'No database connected!'));

    $projects = $ProjectManager->getAllProjects();
    $user = $session->get('User');

    try {
        if (!empty($twig)) {

            echo $twig->render('projects.twig', array('projects' => $projects, 'ProjectManager' => $ProjectManager,
                'User' => $user ));
        }
    } catch (LoaderError | RuntimeError | SyntaxError $e) {
    }



