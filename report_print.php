<?php

require_once "includes.php";
define('FILENAME_TAG', 'image');

/* Denne Twig funksjonen er tatt fra https://stackoverflow.com/questions/61407758/how-to-change-one-value-in-get-by-clicking-a-link-or-button-from-twig-with/61407993#61407993 */
$twig->addFunction(new \Twig\TwigFunction('get_page_url', function ($query = [], $append = true) {
    $tmp = $append ? $_GET : [];
    foreach ($query as $key => $value) $tmp[$key] = $value;

    return '?' . http_build_query($tmp);
}));

$projectManager = new ProjectManager($db, $request, $session);
$userManager = new UserManager($db, $request, $session);
$taskManager = new TaskManager($db, $request, $session);
$hourManager = new HourManager($db, $request, $session);

$project = $projectManager->getProject($request->query->getInt('projectid'));

if ($user->isProjectLeader() or $user->isGroupleader()) {
    $projectName = $project->getProjectName();
    $userID = $request->query->getInt('userID');
    $users = $userManager->getAllUsers("firstName"); //alle brukere
    $customers = $userManager->getAllCustomers("firstName"); //alle kunder
    $employees = $userManager->getAllEmployees("firstName"); //alle arbeidere
    $members = $projectManager->getProjectMembers($project->getProjectName()); //alle medlemmer av dette prosjektet
    $candidates = $projectManager->getLeaderCandidates($projectName); //alle som kan bli prosjektleder

    $tasks = $taskManager->getAllTasks(hasSubtask: 1, projectName: $projectName);
    $phases = $projectManager->getAllPhases($projectName);

    $hours = $hourManager->getHours();
    $groups = $projectManager->getGroups($projectName);
    $groupFromUserAndGroups = $projectManager->getGroupFromUserAndGroups($projectName); //henter gruppe basert på UsersAndGroups tabell. Joiner Group tabell og sjekker prosjektname


    echo $twig->render('report_print.twig',
        array('session' => $session, 'request' => $request, 'user' => $user,

            'users' => $users,
            'customers' => $customers, 'members' => $members,
            'employees' => $employees, 'candidates' => $candidates,

            'project' => $project,

            'phases' => $phases, 'tasks' => $tasks,
            'hours' => $hours,
            'groups' => $groups,
            'groupFromUserAndGroups' => $groupFromUserAndGroups,
            'hourManager' => $hourManager));
} else {
    header("location:index.php");
    exit();
}