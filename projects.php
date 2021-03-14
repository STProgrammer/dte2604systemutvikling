<?php

use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

require_once "includes.php";
define('FILENAME_TAG', 'image');

    if (isset($db)) {
        $Timereg = new Timereg($db, $request, $session);
    }else echo $twig->render('error.twig', array('msg' => 'No database connected!'));

    $projects = $Timereg->getAllProjects();

    try {
        if (!empty($twig)) {
            echo $twig->render('projects.twig', array('projects' => $projects, 'Timereg' => $Timereg));
        }
    } catch (LoaderError | RuntimeError | SyntaxError $e) {
    }



