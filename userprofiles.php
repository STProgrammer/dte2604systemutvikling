<?php

require_once('includes.php');


$Userprofiles = new Userprofiles($db, $request, $session);

define('FILENAME_TAG', 'image');


$users = $Userprofiles->getAllEmployees();

try {
    if (!empty($twig)) {
        echo $twig->render('userprofiles.twig', array('users'=>$users));
    }
} catch (LoaderError | RuntimeError | SyntaxError $e) {
}



