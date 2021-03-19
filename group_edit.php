<?php

require_once('includes.php');


$groupManager = new GroupManager($db, $request, $session);
$userManager = new UserManager($db, $request, $session);

$employees = $userManager->getAllEmployees("firstName");
$group = $groupManager->getGroup($request->query->getInt('groupid'));

if ($user && $group && ($user->isAdmin() or $user->getUserID() == $group->getGroupLeader())) {
    if ($request->request->has('group_edit') && XsrfProtection::verifyMac("Group edit")) {
        if (!$user->isAdmin()) {
            $request->request->set('isAdmin', 0);
        }
        if ($groupManager->editGroup($group)) {
            header("Location: groups.php?editedgroup=1");
            exit();
        } else {
            header("Location: ?failedtoeditgroup=1");
            exit();
        }

    }
    else if ($request->request->has('group_delete') && $user->isAdmin()) {
        if ($groupManager->deleteGoup($group)) {
            header("Location: groups.php?deleteddgroup=1");
            exit();
        } else {
            header("Location: ?failedtodeletegroup=1");
            exit();
        }
    }
    else {
        try {
            echo $twig->render('group_edit.twig', array('session' => $session,
                'request' => $request, 'user' => $user, 'employees' => $employees,
                'group' => $group));
        } catch (LoaderError | \Twig\Error\RuntimeError | \Twig\Error\SyntaxError $e) {
        }
    }
} else {
    header("location: index.php");
    exit();
}


?>