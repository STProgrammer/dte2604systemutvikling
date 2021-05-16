<?php

require_once('includes.php');


$groupManager = new GroupManager($db, $request, $session);
$userManager = new UserManager($db, $request, $session);


$employees = $userManager->getAllEmployees("firstName");


if (!is_null($user) && ($user->isAdmin() | $user->isProjectLeader())) {
    if ($request->request->has('group_register') && XsrfProtection::verifyMac("Group register")) {
        if (!$user->isAdmin()) {
            $request->request->set('isAdmin', 0);
        }
        if ($groupManager->newGroup()) {
            header("Location: groups.php?registeredgroup=1");
            exit();
        } else {
            header("Location: groups.php?failedtoregistergroup=1");
            exit();
        }

    } else {
        try {
            echo $twig->render('group_register.twig', array('session' => $session,
                'request' => $request, 'user' => $user, 'employees' => $employees));
        } catch (\Twig\Error\LoaderError | \Twig\Error\RuntimeError | \Twig\Error\SyntaxError $e) {
        }
    }
} else {
    header("location: index.php");
    exit();
}


?>