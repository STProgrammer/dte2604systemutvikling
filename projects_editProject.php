<?php

require_once "includes.php";
define('FILENAME_TAG', 'image');

$projectManager = new ProjectManager($db, $request, $session);
$userManager = new UserManager($db, $request, $session);

$project = $projectManager->getProject($request->query->getInt('projectid'));


if (!is_null($user) && !is_null($project) && ($user->isAdmin() or $user->isProjectLeader())) {
    $projectName = $project->getProjectName();
    $customers = $userManager->getAllCustomers("firstName"); //alle kunder
    $employees = $userManager->getAllEmployees("firstName");
    $groups = $projectManager->getGroups($projectName);
    $users = $userManager->getAllUsers("firstName"); //alle brukere
    $members = $projectManager->getProjectMembers($project->getProjectName());
    if ($request->request->has('project_edit') && XsrfProtection::verifyMac("Project edit")) {
        if ($projectManager->editProject($project)) {
            header("Location: ".$request->server->get('REQUEST_URI'));
            exit();
        } else {
            header("Location: ?failedtoeditproject");
            exit();
        }
    }
    else if ($request->request->has('group_add') && $user->isAdmin()) {
        if (!$user->isAdmin()) {
            $request->request->set('isAdmin', 0);
        }
        if ($projectManager->addGroup($project->getProjectName()) && XsrfProtection::verifyMac("Add group")) {
            header("Location: ".$request->server->get('REQUEST_URI'));
            exit();
        } else {
            header("Location: ?failedtoaddmembers");
            exit();
        }
    }
    else if ($request->request->has('remove_members') && $user->isAdmin()) {
        if ($projectManager->removeEmployees($project) && XsrfProtection::verifyMac("Project remove members")) {
            header("Location: ".$request->server->get('REQUEST_URI'));
            exit();
        } else {
            header("Location: ?failedtoremovemembers");
            exit();
        }
    }
    else if ($request->request->has('project_delete') && $user->isAdmin()) {
        if ($projectManager->deleteProject($request->query->get('projectName')) && XsrfProtection::verifyMac("Delete project")) {
            header("Location: ".$request->server->get('REQUEST_URI'));
            exit();
        } else {
            header("Location: ?failedtodeleteprojects");
            exit();
        }
    }
    else {
        try {
            echo $twig->render('projects_editProject.twig',
                array('session' => $session, 'request' => $request, 'user' => $user, 'users' => $users,
                    'customers' => $customers, 'project' => $project,  'members' => $members,
                    'employees' => $employees, 'groups' => $groups));
        } catch (LoaderError | \Twig\Error\RuntimeError | \Twig\Error\SyntaxError $e) {
        }
    }
} else {
    header("location: index.php");
    exit();
}


