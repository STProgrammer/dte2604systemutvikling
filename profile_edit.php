<?php

require_once('includes.php');

include('user_register_check.php');

$userManager = new UserManager($db, $request, $session);


if ($user && ($user->isAdmin() | $user->isProjectLeader())) {
    // Change user details
    if ($request->request->has('profile_edit') && XsrfProtection::verifyMac("Edit my information")) {
        if ($userManager->editMyProfile($user)) {
            header("Location: ?useredit=1");
            exit();
        } else {
            header("Location: ?failedtoedituser=1");
            exit();
        }
    }
    //Change email
    elseif ($request->request->has('edit_email') && XsrfProtection::verifyMac("Edit my email address")) {
        if ($userManager->editMyEmailAddress($user)) {
            header("Location: ?emailedit=1");
            exit();
        } else {
            header("Location: ?failedtoeditemail=1");
            exit();
        }
    }
    //Change username
    elseif ($request->request->has('edit_username') && XsrfProtection::verifyMac("Edit my username")) {
        if ($userManager->editMyUsername($user)) {
            header("Location: ?usernameedit=1");
            exit();
        } else {
            header("Location: ?failedtoeditusername=1");
            exit();
        }
    }
    //Change password
    elseif ($request->request->has('edit_password') && XsrfProtection::verifyMac("Edit my password")) {
        if ($userManager->editPassword($user)) {
            header("Location: ?passwordedit=1");
            exit();
        } else {
            header("Location: ?failedtoeditpassword=1");
            exit();
        }
    }
    else {
        try {
            echo $twig->render('profile_edit.twig', array('session' => $session,
                'request' => $request, 'user' => $user));
        } catch (LoaderError | \Twig\Error\RuntimeError | \Twig\Error\SyntaxError $e) {

        }
    }
} else {
    header("location: login.php");
    exit();
}