<?php

require_once('includes.php');


$userManager = new UserManager($db, $request, $session);

if (!is_null($user)) {
    if ($user->isAdmin() && $request->query->has('verify')) {
        $users = $userManager->getUnverifiedUsers("lastName");
        try {
            echo $twig->render('user_verify.twig',
                array('user' => $user, 'users' => $users, 'session' => $session, 'request' => $request));

        } catch (LoaderError | RuntimeError | SyntaxError $e) { echo $e->getMessage(); }
    }
    else {
        $users = $userManager->getAllUsers("lastName");
        try {
            echo $twig->render('userprofiles.twig',
                array('user' => $user, 'users' => $users, 'session' => $session, 'request' => $request));

        } catch (LoaderError | RuntimeError | SyntaxError $e) { echo $e->getMessage(); }
    }
}
else {
    header("location: index.php");
    exit();
}
