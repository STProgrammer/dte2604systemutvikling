<?php

require_once "includes.php";

$projectManager = new ProjectManager($db, $request, $session);
$userManager = new UserManager($db, $request, $session);
$taskManager = new TaskManager($db, $request, $session);

$phase = $projectManager->getPhase($request->query->getInt('phaseid'));

$project = $projectManager->getProjectByName($phase->getProjectName());
$projectName =  $phase->getProjectName();
$isMember = $projectManager->checkIfMemberOfProject($projectName, $user->getUserID());

if (!is_null($user) && !is_null($phase) && ($user->isAdmin() or $user->isProjectLeader() or $isMember)) {

    $projectName =  $phase->getProjectName();

    $phaseId = $phase->getPhaseID();
    $phases = $projectManager->getAllPhases($projectName);

    $employees = $userManager->getAllEmployees("firstName"); //alle arbeidere
    $members = $projectManager->getProjectMembers($project->getProjectName());

    $isMember = $projectManager->checkIfMemberOfProject($projectName, $user->getUserID());

    $tasks = $taskManager->getAllTasks( 0, $projectName);
    $phaseTasks = $taskManager->getAllTasks(0, null, $phaseId);

    if (!(($isMember and $user->getUserType() > 0) or $user->isAdmin() or $user->isProjectLeader())) {
        header("location: projects.php");
        exit();
    }


    if ($request->request->has('phase_edit') && XsrfProtection::verifyMac("Edit phase")) {
        if ($projectManager->editPhase($phase, $project)) {
            header("Location: ".$request->server->get('REQUEST_URI'));
            exit();
        } else {
            header("Location: ".$request->server->get('REQUEST_URI')."?failedtoeditphase");
            exit();
        }
    }
    else if ($request->request->has('tasks_add') && XsrfProtection::verifyMac("Add tasks to phase")) {
        if ($taskManager->addTasksToPhase($phaseId)) {
            header("Location: ".$request->server->get('REQUEST_URI'));
            exit();
        } else {
            header("Location: ".$requestUri."&failedtoaddtasks=1");
            exit();
        }
    }
    else if ($request->request->has('tasks_remove') && XsrfProtection::verifyMac("Remove tasks from phase")) {
        if ($taskManager->removeTasksFromPhase($phaseId)) {
            header("Location: ".$requestUri."&tasksremoved=1");
            exit();
        } else {
            header("Location: ".$requestUri."&failedtoaddtasks=1");
            exit();
        }
    }
    else if ($request->request->has('phase_delete') && XsrfProtection::verifyMac("Delete phase")) {
        if ($projectManager->deletePhase($phaseId)) {
            header("Location: projects_editProject.php?projectid=".$project->getProjectID()."&phasedeleted=1");
            exit();
        } else {
            header("Location: ".$requestUri."&failedtodeletephase=1");
            exit();
        }
    }
    else {
        if ($isMember or $user->isAdmin() or $user->isProjectLeader() or $user->isGroupLeader()) {
            try {
                echo $twig->render('phases_editPhase.twig',
                    array('session' => $session, 'request' => $request, 'user' => $user,
                        'project' => $project,  'members' => $members,
                        'employees' => $employees, 'phase' => $phase,
                        'phases' => $phases, 'tasks' => $tasks, 'phaseTasks' => $phaseTasks));
            } catch (\Twig\Error\LoaderError | \Twig\Error\RuntimeError | \Twig\Error\SyntaxError $e) {
                echo $e->getMessage();
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


