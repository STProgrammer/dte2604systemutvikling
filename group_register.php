<?php

require_once('includes.php');


$groupManager = new GroupManager($db, $request, $session);
$userManager = new UserManager($db, $request, $session);


$employees = $userManager->getAllEmployees("firstName");


if ($user && ($user->isAdmin() | $user->isProjectLeader())) {
    if ($request->request->has('group_register') && XsrfProtection::verifyMac("Group register")) {
        if (!$user->isAdmin()) {
            $request->request->set('isAdmin', 0);
        }
        if ($groupManager->newGroup()) {
            header("Location: groups.php?registeredgroup=1");
            exit();
        } else {
            header("Location: ?failedtoregistergroup=1");
            exit();
        }

    } else {
        try {
            echo $twig->render('group_register.twig', array('session' => $session,
                'request' => $request, 'user' => $user, 'employees' => $employees));
        } catch (LoaderError | \Twig\Error\RuntimeError | \Twig\Error\SyntaxError $e) {
            echo $e->getMessage();
        }
    }
} else {
    header("location: index.php");
    exit();
}


?>