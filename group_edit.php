<?php

require_once('includes.php');


$groupManager = new GroupManager($db, $request, $session);
$userManager = new UserManager($db, $request, $session);

//$employees = $userManager->getAllEmployees("firstName");
$group = $groupManager->getGroup($request->query->getInt('groupid'));


if (!is_null($user) && !is_null($group) && ($user->isAdmin() or $user->getUserID() == $group->getGroupLeader())) {
    $groupID = $group->getGroupID();
    $employees = $groupManager->getAllNonMembers($groupID);
    $members = $groupManager->getGroupMembers($groupID);
    $candidates = $groupManager->getLeaderCandidates($groupID);
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
    else if ($request->request->has('add_members') && $user->isAdmin()) {
        if ($groupManager->addEmployees($groupID) && XsrfProtection::verifyMac("Group add members")) {
            header("Location: groups.php?addmembers=1");
            exit();
        } else {
            header("Location: ?failedtoaddmembers=1");
            exit();
        }
    }
    else if ($request->request->has('remove_members') && $user->isAdmin()) {
        if ($groupManager->removeEmployees($group) && XsrfProtection::verifyMac("Group remove members")) {
            header("Location: groups.php?removemembers=1");
            exit();
        } else {
            header("Location: ?failedtoremovemembers=1");
            exit();
        }
    }
    else if ($request->request->has('group_delete') && $user->isAdmin()) {
        if ($groupManager->deleteGroup($group) && XsrfProtection::verifyMac("Delete group")) {
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
                'group' => $group, 'members' => $members, 'candidates' => $candidates));
        } catch (LoaderError | \Twig\Error\RuntimeError | \Twig\Error\SyntaxError $e) {
            echo $e->getMessage();
        }
    }
} else {
    header("location: index.php");
    exit();
}


?>