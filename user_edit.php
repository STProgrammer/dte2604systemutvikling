<?php

require_once('includes.php');


//Denne siden er det selve innloggede brukeren skal ha tilgang til

$userManager = new UserManager($db, $request, $session);


if ($user && ($user->isAdmin() | $user->isProjectLeader())) {
    // Change user details
    $userToEdit = $userManager->getUser($request->query->getInt('userid'));
    if ($request->request->has('user_edit') && XsrfProtection::verifyMac("Edit user's information")) {
        //Only admins can make other users admin, cheating not allowed
        $userType = $request->request->getInt('userType');
        if (!$user->isAdmin() && $userType == 3) {
            $request->request->set('userType', 2);
        }
        if ($userManager->editOtherUser($userToEdit)) {
            header("Location: ".$requestUri."&useredited=1");
            exit();
        } else {
            header("Location: ".$requestUri."&failedtoedituser=1");
            exit();
        }
    }
    //Delete user
    else if ($request->request->has('delete_user') && XsrfProtection::verifyMac("Delete user") && $user->isAdmin()) {
        if ($userManager->deleteUser($userToEdit->getUserId())) {
            header("Location: userprofiles.php?userdeleted=1");
            exit();
        } else {
            header("Location: ".$requestUri."&failedtodeleteuser=1");
            exit();
        }
    }
    else {
        try {
            echo $twig->render('user_edit.twig', array('session' => $session,
                'request' => $request, 'user' => $user, 'userToEdit' => $userToEdit));
        } catch (LoaderError | \Twig\Error\RuntimeError | \Twig\Error\SyntaxError $e) { echo $e->getMessage(); }
    }
} else {
    header("location: login.php");
    exit();
}