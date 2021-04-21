<?php

require_once "includes.php";



if (!is_null($user) and ($user->isAdmin() or $user->isProjectLeader() or $user->isGroupleader())) {
    $projectManager = new ProjectManager($db, $request, $session);
    $userManager = new UserManager($db, $request, $session);
    $taskManager = new TaskManager($db, $request, $session);
    $hourManager = new HourManager($db, $request, $session);

    $project = $projectManager->getProject($request->query->getInt('projectid'));

    $projectName = $project->getProjectName();
    $userID = $request->query->getInt('userID');
    $users = $userManager->getAllUsers("firstName"); //alle brukere
    $customers = $userManager->getAllCustomers("firstName"); //alle kunder
    $employees = $userManager->getAllEmployees("firstName"); //alle arbeidere
    $members = $projectManager->getProjectMembers($project->getProjectName()); //alle medlemmer av dette prosjektet
    $candidates = $projectManager->getLeaderCandidates($projectName); //alle som kan bli prosjektleder

    $tasks = $taskManager->getAllTasks();
    $phases = $projectManager->getAllPhases($projectName);

    $hours = $hourManager->getAllHours();
    $groups = $projectManager->getGroups($projectName);
    $groupFromUserAndGroups = $projectManager->getGroupFromUserAndGroups($projectName); //henter gruppe basert på UsersAndGroups tabell. Joiner Group tabell og sjekker prosjektname

    $totalTimeWorked = $hourManager->totalTimeWorked($projectName);


    echo $twig->render('report_print.twig',
        array('session' => $session, 'request' => $request, 'user' => $user,

            'users' => $users,
            'customers' => $customers, 'members' => $members,
            'employees' => $employees, 'candidates' => $candidates,

            'project' => $project,
            'totalTimeWorked' => $totalTimeWorked,
            'phases' => $phases, 'tasks' => $tasks,
            'hours' => $hours,
            'groups' => $groups,
            'groupFromUserAndGroups' => $groupFromUserAndGroups,
            'hourManager' => $hourManager));
} else {
    header("location: index.php");
    exit();
}