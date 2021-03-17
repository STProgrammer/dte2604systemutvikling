<?php

require_once('includes.php');

define('FILENAME_TAG', 'image');


$userManager = new UserManager($db, $request, $session);
$user = $session;

if ($user =! null) {
    $users = $userManager->getAllUsers("lastName");
    try {
        echo $twig->render('userprofiles.twig', array('user' => $user, 'users' => $users, 'session' => $session, 'request' => $request));
    } catch (LoaderError | RuntimeError | SyntaxError $e) {  }
        }
else {
    header("location: login.php");
    exit();
}
