<?php

require_once('includes.php');


//Denne siden er det selve innloggede brukeren skal ha tilgang til

$userManager = new UserManager($db, $request, $session);


if ($user && ($user->isAdmin() | $user->isProjectLeader())) {
    // Change user details
    $userToEdit = $userManager->getUser($request->query->getInt('userid'));
    if ($request->request->has('user_edit') && XsrfProtection::verifyMac("Edit user's information")) {
        //ONLY ADMIN CAN MAKE ANOTHER USER ADMIN
        if (!$user->isAdmin()) {
            $request->request->set('isAdmin', 0);
        }
        if ($userManager->editOtherUser($userToEdit)) {
            header("Location: ".$request->server->get('REQUEST_URI'));
            exit();
        } else {
            header("Location: ".$request->server->get('REQUEST_URI'));
            exit();
        }
    }
    //Delete user
    else if ($request->request->has('delete_user') && XsrfProtection::verifyMac("Delete user") && $user->isAdmin()) {
        if ($userManager->deleteUser($userToEdit->getUserId())) {
            header("Location: ".$request->server->get('HTTP_REFERRER'));
            exit();
        } else {
            header("Location: ".$request->server->get('HTTP_REFERRER'));
            exit();
        }
    }
    else {
        try {
            echo $twig->render('user_edit.twig', array('session' => $session,
                'request' => $request, 'user' => $user, 'userToEdit' => $userToEdit));
        } catch (LoaderError | \Twig\Error\RuntimeError | \Twig\Error\SyntaxError $e) {  }
    }
} else {
    header("location: login.php");
    exit();
}