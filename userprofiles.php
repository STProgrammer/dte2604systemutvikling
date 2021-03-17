<?php

require_once('includes.php');


//$Userprofiles = new Userprofiles($db, $request, $session);

define('FILENAME_TAG', 'image');


$userManager = new UserManager($db, $request, $session);

$users = $userManager->getAllUsers("lastName");

try {
    if (!empty($twig)) {
        echo $twig->render('userprofiles.twig', array('users'=>$users, 'session' => $session));
    }
} catch (LoaderError | RuntimeError | SyntaxError $e) {
}



