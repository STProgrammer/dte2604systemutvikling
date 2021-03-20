<?php

require_once "includes.php";
define('FILENAME_TAG', 'image');

$ProjectManager = new ProjectManager($db, $request, $session);
$userManager = new UserManager($db, $request, $session);

$project = $ProjectManager->getProject($request->query->get('projectName'));
$projectName = $project->getProjectName();
$employees = $userManager->getAllEmployees("firstName"); //alle som ikke er kunde
$users = $userManager->getAllUsers("firstName"); //alle brukere
$members = $ProjectManager->getProjectMembers($project->getProjectName());

if ($user && $project && ($user->isAdmin() or $user->isProjectLeader())) {
    if ($request->request->has('project_edit') && XsrfProtection::verifyMac("Project edit")) {
        if (!$user->isAdmin()) {
            $request->request->set('isAdmin', 0);
        }
        if ($ProjectManager->editProject($project)) {
            header("Location: projects_editProject.php?projectName="+$projectName);
            exit();
        } else {
            header("Location: ?failedtoeditproject=1");
            exit();
        }
    }
    else if ($request->request->has('add_members') && $user->isAdmin()) {
        if ($ProjectManager->addEmployees($projectName) && XsrfProtection::verifyMac("Project add members")) {
            header("Location: projects.php?addmembers=1");
            exit();
        } else {
            header("Location: ?failedtoaddmembers=1");
            exit();
        }
    }
    else if ($request->request->has('remove_members') && $user->isAdmin()) {
        if ($ProjectManager->removeEmployees($project) && XsrfProtection::verifyMac("Project remove members")) {
            header("Location: projects.php?removemembers=1");
            exit();
        } else {
            header("Location: ?failedtoremovemembers=1");
            exit();
        }
    }
    else if ($request->request->has('project_delete') && $user->isAdmin()) {
        if ($ProjectManager->deleteGoup($project) && XsrfProtection::verifyMac("Delete project")) {
            header("Location: projects.php?deleteddproject=1");
            exit();
        } else {
            header("Location: ?failedtodeleteprojects=1");
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


