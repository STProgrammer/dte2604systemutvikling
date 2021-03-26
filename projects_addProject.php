<?php

require_once "includes.php";
define('FILENAME_TAG', 'image');

$ProjectManager = new ProjectManager($db, $request, $session);
$projects = $ProjectManager->getAllProjects();

$user = $session->get('User');

$userManager = new UserManager($db, $request, $session);
$customers = $userManager->getAllCustomers("lastName");

if ($user) {
    if ($request->request->has('addProject') && $user->isAdmin() | $user->isProjectLeader()
        && XsrfProtection::verifyMac("addProject")) {
        $ProjectManager->addProject();
        header("projects.php");
        exit();

    } else {
        echo $twig->render('projects_addProject.twig',
            array('projects' => $projects, 'ProjectManager' => $ProjectManager, 'session' => $session,
                'User' => $user,  'customers' => $customers));
    }

} else {
    header("location: login.php");
    exit();
}

