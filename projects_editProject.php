<?php

require_once "includes.php";

/* Denne Twig funksjonen er tatt fra https://stackoverflow.com/questions/61407758/how-to-change-one-value-in-get-by-clicking-a-link-or-button-from-twig-with/61407993#61407993 */
$twig->addFunction(new \Twig\TwigFunction('get_page_url', function($query = [], $append = true) {
    $tmp = $append ? $_GET : [];
    foreach($query as $key => $value) $tmp[$key] = $value;

    return '?' . http_build_query($tmp);
}));

$projectManager = new ProjectManager($db, $request, $session);
$userManager = new UserManager($db, $request, $session);
$taskManager = new TaskManager($db, $request, $session);

$project = $projectManager->getProject($request->query->getInt('projectid'));


if (!is_null($user) && !is_null($project)) {
    $projectName = $project->getProjectName();

    $users = $userManager->getAllUsers("firstName"); //alle brukere
    $customers = $userManager->getAllCustomers("firstName"); //alle kunder
    $employees = $userManager->getAllEmployees("firstName"); //alle arbeidere
    $members = $projectManager->getProjectMembers($project->getProjectName()); //alle medlemmer av dette prosjektet
    $candidates = $projectManager->getLeaderCandidates($projectName); //alle som kan bli prosjektleder

    $tasks = $taskManager->getAllTasks(hasSubtask: 1, projectName: $projectName);
    $phases = $projectManager->getAllPhases($projectName);

    $groups = $projectManager->getGroups($projectName);
    $groupFromUserAndGroups = $projectManager->getGroupFromUserAndGroups($projectName); //henter gruppe basert på UsersAndGroups tabell. Joiner Group tabell og sjekker prosjektname



    if ($user->isAdmin()  && $request->request->has('project_edit') && XsrfProtection::verifyMac("Project edit")) {
        if ($projectManager->editProject($project)) {
            header("Location: ".$request->server->get('REQUEST_URI'));
            exit();
        } else {
            header("Location: ?failedtoeditproject");
            exit();
        }
    }
    else if ($user->isAdmin() && $request->request->has('group_add') && XsrfProtection::verifyMac("Add group")) {
        if (!$user->isAdmin()) {
            $request->request->set('isAdmin', 0);
        }
        if ($projectManager->addGroup($project->getProjectName())) {
            header("Location: ".$request->server->get('REQUEST_URI'));
            exit();
        } else {
            header("Location: ?failedtoaddmembers");
            exit();
        }
    }
    else if ($user->isAdmin() && $request->request->has('project_delete')) {
        if ($projectManager->deleteProject($projectName) && XsrfProtection::verifyMac("Delete project")) {
            header("Location: ".$request->server->get('REQUEST_URI'));
            exit();
        } else {
            header("Location: ?failedtodeleteprojects");
            exit();
        }
    }
    else if ($user->isAdmin() && $request->request->has('project_verify') && XsrfProtection::verifyMac("Verify project")) {
        if ($projectManager->verifyProjectByAdmin($project->getProjectID())) {
            header("Location: ".$request->server->get('REQUEST_URI'));
            exit();
        } else {
            header("Location: ".$request->server->get('REQUEST_URI')."&failedtoverifyproject=!");
            exit();
        }
    }
    else if ($user->isAdmin()  && $request->request->has('new_task') && XsrfProtection::verifyMac("New task")) {
        if ($taskManager->addMainTask($projectName)) {
            header("Location: ".$request->server->get('REQUEST_URI'));
            exit();
        } else {
            header("Location: ?failedtoaddtasks");
            exit();
        }
    }
    else if ($user->isAdmin()  && $request->request->has('remove_member') && XsrfProtection::verifyMac("Project remove member")) {
        if ($projectManager->removeMember($project)) {
            header("Location: ".$request->server->get('REQUEST_URI'));
            exit();
        } else {
            header("Location: ?failedtoremovemembers");
            exit();
        }
    }
    else if ($user->isAdmin() && $request->request->has('phase_add') && XsrfProtection::verifyMac("Add phase")) {
        if ($projectManager->addPhase($project)) {
            header("Location: ".$request->server->get('REQUEST_URI'));
            exit();
        } else {
            header("Location: ".$request->server->get('REQUEST_URI')."&failedtaddphase=!");
            exit();
        }
    }
    else {
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
        } catch (\Twig\Error\LoaderError  | \Twig\Error\RuntimeError | \Twig\Error\SyntaxError $e) {
            echo $e->getMessage();
        }
    }
} else {
    header("location: index.php");
    exit();
}


