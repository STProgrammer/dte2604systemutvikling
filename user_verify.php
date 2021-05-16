<?php

require_once('includes.php');


//Denne siden er det selve innloggede brukeren skal ha tilgang til

$userManager = new UserManager($db, $request, $session);


if (!is_null($user) && $user->isAdmin()) {
    // Change user details
    $userToVerify = $userManager->getUser($request->query->getInt('userid'));
    if ($request->request->has('user_verify') && XsrfProtection::verifyMac("Verify user")) {
        //Only admins can make other users admin, cheating not allowed
        if ($userManager->verifyUserByAdmin($userToVerify->getUserId())) {
            header("Location: ".$request->server->get('REQUEST_URI'));
            exit();
        } else {
            header("Location: userprofiles.php");
            exit();
        }
    }
    else {
        header("Location: userprofiles.php");
        exit();
    }
} else {
    header("location: login.php");
    exit();
}