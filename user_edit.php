<?php

require_once('includes.php');


//Denne siden er det selve innloggede brukeren skal ha tilgang til

$userManager = new UserManager($db, $request, $session);
$userToEdit = $userManager->getUser($request->query->getInt('userid', 0));

if (!is_null($user) && ($user->isAdmin() | $user->isProjectLeader()) && !is_null($userToEdit)) {
    // Change user details
    $hours = $userManager->getUserStatistics($userToEdit->getUserID());


    if ($request->request->has('user_edit') && XsrfProtection::verifyMac("Edit user's information")) {
        //Only admins can make other users admin, cheating not allowed
        $userType = $request->request->getInt('userType', $userToEdit->getUserType());
        if (!$user->isAdmin() && $userType == 3) {
            $request->request->set('userType', 2);
        }
        if ($userManager->editOtherUser($userToEdit)) {
            header("Location: ".$requestUri);
            exit();
        } else {
            header("Location: ".$requestUri);
            exit();
        }
    }
    //Delete user
    else if ($request->request->has('delete_user') && XsrfProtection::verifyMac("Delete user") && $user->isAdmin()) {
        if ($userManager->deleteUser($userToEdit->getUserId())) {
            header("Location: userprofiles.php");
            exit();
        } else {
            header("Location: ".$requestUri);
            exit();
        }
    }
    //Edit password
    else if ($request->request->has('user_edit_password') && XsrfProtection::verifyMac("Edit user's password") && $user->isAdmin()) {
        if ($userManager->editPassword($userToEdit->getUserId())) {
            header("Location: userprofiles.php");
        } else {
            header("Location: userprofiles.php");
        }
    }
    else {
        try {
            echo $twig->render('user_edit.twig', array('session' => $session,
                'request' => $request, 'user' => $user, 'userToEdit' => $userToEdit,
                'hours' => $hours));
        } catch (\Twig\Error\LoaderError | \Twig\Error\RuntimeError | \Twig\Error\SyntaxError $e) { }
    }
} else {
    header("location: userprofiles.php");
    exit();
}