<?php

require_once('includes.php');


$groupManager = new GroupManager($db, $request, $session);
$userManager = new UserManager($db, $request, $session);
$projectManager = new ProjectManager($db, $request, $session);
$taskManager = new TaskManager($db, $request, $session);

//$employees = $userManager->getAllEmployees("firstName");
$group = $groupManager->getGroup($request->query->getInt('groupid'));


if (!is_null($user) && !is_null($group) && ($user->isAdmin() or $user->getUserID() == $group->getGroupLeader())) {
    $groupID = $group->getGroupID();
    $employees = $groupManager->getAllNonMembers($groupID);
    $members = $groupManager->getGroupMembers($groupID);
    $candidates = $groupManager->getLeaderCandidates($groupID);
    $projects = $projectManager->getAllProjects();
    $tasks = $taskManager->getAllTasks(hasSubtask: 1, projectName: $group->getProjectName(), groupID: $groupID);
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
    else if ($request->request->has('add_members') && XsrfProtection::verifyMac("Group add members")) {
        if ($groupManager->addEmployees($groupID)) {
            header("Location: groups.php?addmembers=1");
            exit();
        } else {
            header("Location: ?failedtoaddmembers=1");
            exit();
        }
    }
    else if ($request->request->has('remove_members') && XsrfProtection::verifyMac("Group remove members")) {
        if ($groupManager->removeEmployees($group)) {
            header("Location: groups.php?removemembers=1");
            exit();
        } else {
            header("Location: ?failedtoremovemembers=1");
            exit();
        }
    }
    else if ($request->request->has('add_to_project') && XsrfProtection::verifyMac("Add group to project")) {
        if ($groupManager->addToProject($group)) {
            header("Location: groups.php?addtoproject=1");
            exit();
        } else {
            header("Location: ?failedtoaddtoproject=1");
            exit();
        }
    }
    else if ($request->request->has('group_delete') && $user->isAdmin() && XsrfProtection::verifyMac("Delete group")) {
        if ($groupManager->deleteGroup($group)) {
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
                'group' => $group, 'members' => $members, 'candidates' => $candidates,
            'projects' => $projects, 'tasks' => $tasks));
        } catch (LoaderError | \Twig\Error\RuntimeError | \Twig\Error\SyntaxError $e) {
            echo $e->getMessage();
        }
    }
} else {
    header("location: index.php");
    exit();
}


?>