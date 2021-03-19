<?php

require_once('includes.php');


$userManager = new UserManager($db, $request, $session);

if (!is_null($user)) {
    $users = $userManager->getAllUsers("lastName");
    try {
        echo $twig->render('userprofiles.twig', array('user' => $user, 'users' => $users, 'session' => $session, 'request' => $request));
    } catch (LoaderError | RuntimeError | SyntaxError $e) {  }
        }
else {
    header("location: login.php");
    exit();
}
