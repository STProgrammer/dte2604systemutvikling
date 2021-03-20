<?php

require_once('includes.php');

include('user_register_check.php');

$userManager = new UserManager($db, $request, $session);


if ($user && ($user->isAdmin() | $user->isProjectLeader())) {
    if ($request->request->has('register') && XsrfProtection::verifyMac("user_register")) {
        //Bare admin kan registrere admin
        if ($userManager->registerUser($user)) {
            header("Location: userprofiles.php?registereduser=1");
            exit();
        } else {
            header("Location: ?failedtoregisteruser=1");
            exit();
        }

    } else {
        try {
            echo $twig->render('user_register.twig', array('session' => $session,
                'request' => $request, 'user' => $user));
        } catch (LoaderError | \Twig\Error\RuntimeError | \Twig\Error\SyntaxError $e) {
        }
    }
} else {
    header("location: login.php");
    exit();
}


?>