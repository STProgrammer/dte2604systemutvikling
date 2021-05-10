<?php

require_once('includes.php');

include('user_register_check.php');


//Denne siden er det selve innloggede brukeren skal ha tilgang til

$userManager = new UserManager($db, $request, $session);


if ($user) {
    // Change user details
    $hours = $userManager->getUserStatistics($user->getUserID());

    if ($request->request->has('profile_edit') && XsrfProtection::verifyMac("Edit my information")) {
        if ($userManager->editMyProfile($user)) {
            header("Location: ".$requestUri);
            exit();
        } else {
            header("Location: ".$requestUri);
            exit();
        }
    }
    //Change email
    elseif ($request->request->has('edit_email') && XsrfProtection::verifyMac("Edit my email address")) {
        if ($userManager->editMyEmailAddress($user)) {
            header("Location: ".$requestUri);
            exit();
        } else {
            header("Location: ".$requestUri);
            exit();
        }
    }
    //Change username
    elseif ($request->request->has('edit_username') && XsrfProtection::verifyMac("Edit my username")) {
        if ($userManager->editMyUsername($user)) {
            header("Location: ".$requestUri);
            exit();
        } else {
            header("Location: ".$requestUri);
            exit();
        }
    }
    //Change password
    elseif ($request->request->has('edit_password') && XsrfProtection::verifyMac("Edit my password")) {
        if ($userManager->editPassword($user)) {
            header("Location: ".$requestUri);
            exit();
        } else {
            header("Location: ".$requestUri);
            exit();
        }
    }
    else {
        try {
            echo $twig->render('profile_edit.twig', array('session' => $session,
                'request' => $request, 'user' => $user, 'hours' => $hours));
        } catch (\Twig\Error\LoaderError | \Twig\Error\RuntimeError | \Twig\Error\SyntaxError $e) {
            echo $e->getMessage();
        }
    }
} else {
    header("location: login.php");
    exit();
}