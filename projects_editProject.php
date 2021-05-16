<?php

require_once "includes.php";

$projectManager = new ProjectManager($db, $request, $session);
$userManager = new UserManager($db, $request, $session);
$taskManager = new TaskManager($db, $request, $session);

$project = $projectManager->getProject($request->query->getInt('projectid'));
$isMember = false;

if (!is_null($user) && !is_null($project)) {
    $projectName = $project->getProjectName();

    $users = $userManager->getAllUsers("firstName"); //alle brukere
    $customers = $userManager->getAllCustomers("firstName"); //alle kunder
    $employees = $userManager->getAllEmployees("firstName"); //alle arbeidere
    $members = $projectManager->getProjectMembers($project->getProjectName()); //alle medlemmer av dette prosjektet
    $candidates = $projectManager->getLeaderCandidates($projectName); //alle som kan bli prosjektleder

    $tasks = $taskManager->getAllTasks(1, $projectName);
    $phases = $projectManager->getAllPhases($projectName);

    $groups = $projectManager->getGroups($projectName);
    $groupFromUserAndGroups = $projectManager->getGroupFromUserAndGroups($projectName); //henter gruppe basert på UsersAndGroups tabell. Joiner Group tabell og sjekker prosjektname

    /* Sjekk om bruker er medlem i fremviste prosjekt */
    $isMember = $projectManager->checkIfMemberOfProject($projectName, $user->getUserID());



if ($user->isAdmin() && $request->request->has('project_edit') && XsrfProtection::verifyMac("Project edit")) {
    if ($projectManager->editProject($project)) {
        header("Location: " . $request->server->get('REQUEST_URI'));
        exit();
    } else {
        header("Location: " . $requestUri);
        exit();
    }
} else if ($user->isAdmin() && $request->request->has('group_add') && XsrfProtection::verifyMac("Add group")) {
    if (!$user->isAdmin()) {
        $request->request->set('isAdmin', 0);
    }
    if ($projectManager->addGroup($project->getProjectName())) {
        header("Location: " . $requestUri );
        exit();
    } else {
        header("Location: " . $requestUri);
        exit();
    }
} else if ($user->isAdmin() && $request->request->has('project_delete')) {
    if ($projectManager->deleteProject($projectName) && XsrfProtection::verifyMac("Delete project")) {
        header("Location: projects.php");
        exit();
    } else {
        header("Location: " . $requestUri);
        exit();
    }
} else if ($user->isAdmin() && $request->request->has('project_verify') && XsrfProtection::verifyMac("Verify project")) {
    if ($projectManager->verifyProjectByAdmin($project->getProjectID())) {
        header("Location: " . $requestUri );
        exit();
    } else {
        header("Location: " . $requestUri);
        exit();
    }
} else if (($user->isAdmin() or $user->isProjectLeader()) && $request->request->has('new_task') && XsrfProtection::verifyMac("New task")) {
    if ($taskManager->addMainTask($projectName)) {
        header("Location: " . $requestUri );
        exit();
    } else {
        header("Location: " . $requestUri);
        exit();
    }
} else if ($user->isAdmin() && $request->request->has('remove_member') && XsrfProtection::verifyMac("Project remove member")) {
    if ($projectManager->removeMember($project)) {
        header("Location: " . $requestUri);
        exit();
    } else {
        header("Location: " . $requestUri);
        exit();
    }
} else if (($user->isAdmin() or $user->isProjectLeader()) && $request->request->has('phase_add') && XsrfProtection::verifyMac("Add phase")) {
    if ($projectManager->addPhase($project)) {
        header("Location: " . $requestUri );
        exit();
    } else {
        header("Location: " . $requestUri);
        exit();
    }
} else {
    if ($isMember or $user->isAdmin() or $user->isProjectLeader() or $user->isGroupLeader() or $user->getUserId() ==  $project->getCustomer() ) {
        try {
            echo $twig->render('projects_editProject.twig',
                array('session' => $session, 'request' => $request, 'user' => $user,

                    'users' => $users,
                    'customers' => $customers, 'members' => $members,
                    'employees' => $employees, 'candidates' => $candidates,

                    'project' => $project,

                    'phases' => $phases, 'tasks' => $tasks,

                    'groups' => $groups,
                    'groupFromUserAndGroups' => $groupFromUserAndGroups));
        } catch (\Twig\Error\LoaderError | \Twig\Error\RuntimeError | \Twig\Error\SyntaxError $e) {

        }
    }else {
        header("location: projects.php");
        exit();
    }
}
} else {
    header("location: projects.php");
    exit();
}


