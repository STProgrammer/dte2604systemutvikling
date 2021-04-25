<?php

require_once('includes.php');


$groupManager = new GroupManager($db, $request, $session);
$userManager = new UserManager($db, $request, $session);
$employees = $userManager->getAllEmployees("firstName");

if (!is_null($user)) {
    $groups = $groupManager->getAllGroups();

    $groupsOfUser = [];
    foreach ($groups as $group) {
        $groupMember = $groupManager->getGroupMembers($group->getGroupID());
        foreach ($groupMember as $member) {
            if ($user->getUserId() ==  $member->getUserId()) {
                array_push($groupsOfUser, $group);
            }
        }
    }

    try {
        echo $twig->render('groups.twig',
            array('user' => $user, 'groups' => $groups,  'groupsOfUser' => $groupsOfUser,
            'session' => $session, 'request' => $request, 'employees' => $employees));
    } catch (LoaderError | RuntimeError | SyntaxError $e) { echo $e->getMessage();  }
}
else {
    header("location: index.php");
    exit();
}
