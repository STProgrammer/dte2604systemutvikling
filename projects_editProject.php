<?php

require_once "includes.php";
define('FILENAME_TAG', 'image');

$ProjectManager = new ProjectManager($db, $request, $session);
$projects = $ProjectManager->getAllProjects();

$user = $session->get('User');

$userManager = new UserManager($db, $request, $session);
$users = $userManager->getAllUsers("lastName");

if ($user && ($user->isAdmin() | $user->isProjectLeader())) {
    $projectToEdit = $ProjectManager->getProject($request->query->getInt('projectName'));
    echo $twig->render('projects_editProject.twig',
        array('projects' => $projects, 'ProjectManager' => $ProjectManager, 'session' => $session,
            'User' => $user,  'users' => $users));

    if ($request->request->has('addProject') && $user->isAdmin() | $user->isProjectLeader()
        && XsrfProtection::verifyMac("addProject")) {

        $project = new Project();
        $project->setProjectName($request->request->get('projectName'));
        $project->setProjectLeader($request->request->getInt('projectLeader'));
        $project->setStartTime($request->request->get('startTime'));
        $project->setFinishTime($request->request->get('finishTime'));
        $project->setStatus($request->request->getInt('status'));
        $project->setCustomer($request->request->getInt('customer'));
        $project->setIsAcceptedByAdmin($request->request->getInt('isAcceptedByAdmin'));

        $ProjectManager->addProject($project);
    }

} else {
    header("location: login.php");
    exit();
}

