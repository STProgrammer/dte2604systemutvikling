<?php

require_once "includes.php";
define('FILENAME_TAG', 'image');

$ProjectManager = new ProjectManager($db, $request, $session);
$userManager = new UserManager($db, $request, $session);

$project = $ProjectManager->getProject($request->query->getInt('projectid'));


if (!is_null($user) && !is_null($project) && ($user->isAdmin() or $user->isProjectLeader())) {
    $projectName = $project->getProjectName();
    $employees = $userManager->getAllEmployees("firstName"); //alle som ikke er kunde
    $users = $userManager->getAllUsers("firstName"); //alle brukere
    $members = $ProjectManager->getProjectMembers($project->getProjectName());
    if ($request->request->has('project_edit') && XsrfProtection::verifyMac("Project edit")) {
        if (!$user->isAdmin()) {
            $request->request->set('isAdmin', 0);
        }
        if ($ProjectManager->editProject($project)) {
            header("Location: projects_editProject.php?projectName=".$projectName);
            exit();
        } else {
            header("Location: ?failedtoeditproject");
            exit();
        }
    }
    else if ($request->request->has('add_group') && $user->isAdmin()) {
        if ($ProjectManager->addGroup($project->getProjectID()) && XsrfProtection::verifyMac("Project add group")) {
            header("Location: projects_editProject.php?projectName=".$projectName);
            exit();
        } else {
            header("Location: ?failedtoaddmembers");
            exit();
        }
    }
    else if ($request->request->has('remove_members') && $user->isAdmin()) {
        if ($ProjectManager->removeEmployees($project) && XsrfProtection::verifyMac("Project remove members")) {
            header("Location: ".$request->server->get('REQUEST_URI'));
            exit();
        } else {
            header("Location: ?failedtoremovemembers");
            exit();
        }
    }
    else if ($request->request->has('project_delete') && $user->isAdmin()) {
        if ($ProjectManager->deleteProject($request->query->get('projectName')) && XsrfProtection::verifyMac("Delete project")) {
            header("Location: projects_editProject.php?projectName=".$projectName);
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
                    'employees' => $employees, 'project' => $project,  'members' => $members));
        } catch (LoaderError | \Twig\Error\RuntimeError | \Twig\Error\SyntaxError $e) {
        }
    }
} else {
    header("location: index.php");
    exit();
}


