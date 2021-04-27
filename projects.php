<?php

require_once "includes.php";


if ($user) {
    $ProjectManager = new ProjectManager($db, $request, $session);
    $projects = $ProjectManager->getAllProjects();

    $projectsOfUser = $ProjectManager->getProjectsOfUser($user->getUserID());

    /*foreach ($projects as $project) {
        $projectMember = $ProjectManager->getProjectMembers($project->getProjectName());
        foreach ($projectMember as $member) {
            if ($user->getUserId() ==  $member->getUserId()) {
                array_push($projectsOfUser, $project);
            }
        }
    }*/

    $userManager = new UserManager($db, $request, $session);
    $users = $userManager->getAllUsers("lastName");

    echo $twig->render('projects.twig',
        array('projects' => $projects, 'projectManager' => $ProjectManager, 'session' => $session,
            'user' => $user,  'users' => $users, 'projectsOfUser' => $projectsOfUser));
}
else {
    header("location: index.php");
    exit();
}

