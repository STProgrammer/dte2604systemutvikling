<?php

require_once "includes.php";

$projectManager = new ProjectManager($db, $request, $session);
$userManager = new UserManager($db, $request, $session);
$taskManager = new TaskManager($db, $request, $session);

$phase = $projectManager->getPhase($request->query->getInt('phaseid'));


if (!is_null($user) && !is_null($phase) && ($user->isAdmin() or $user->isProjectLeader())) {
    $project = $projectManager->getProjectByName($phase->getProjectName());
    $projectName =  $phase->getProjectName();
    $phaseId = $phase->getPhaseID();
    $employees = $userManager->getAllEmployees("firstName"); //alle arbeidere
    $phases = $projectManager->getAllPhases($projectName);
    $members = $projectManager->getProjectMembers($project->getProjectName());
    $tasks = $taskManager->getAllTasks();
    if ($request->request->has('phase_edit') && XsrfProtection::verifyMac("Edit phase")) {
        if ($projectManager->editPhase($phase, $project)) {
            header("Location: ".$request->server->get('REQUEST_URI'));
            exit();
        } else {
            header("Location: ".$request->server->get('REQUEST_URI')."?failedtoeditphase");
            exit();
        }
    }
    else if ($request->request->has('task_add') && XsrfProtection::verifyMac("Add task")) {
        if ($taskManager->addTask($phaseId, $projectName)) {
            header("Location: ".$request->server->get('REQUEST_URI'));
            exit();
        } else {
            header("Location: ?failedtoaddtasks");
            exit();
        }
    }
    else if ($request->request->has('remove_task') && XsrfProtection::verifyMac("Remove tasks")) {
        if ($projectManager->removeEmployees($project)) {
            header("Location: ".$request->server->get('REQUEST_URI'));
            exit();
        } else {
            header("Location: ?failedtoremovetasks");
            exit();
        }
    }
    else if ($request->request->has('phase_delete') && XsrfProtection::verifyMac("Delete phase")) {
        if ($projectManager->deletePhase($phaseId)) {
            header("Location: projects_editProject.php?projectid=".$project->getProjectID());
            exit();
        } else {
            header("Location: projects_editProject.php?projectid=".$project->getProjectID()."&failedtodeleteprojects=1");
            exit();
        }
    }
    else {
        try {
            echo $twig->render('phases_editPhase.twig',
                array('session' => $session, 'request' => $request, 'user' => $user,
                    'project' => $project,  'members' => $members,
                    'employees' => $employees, 'phase' => $phase,
                'phases' => $phases, 'tasks' => $tasks));
        } catch (\Twig\Error\LoaderError | \Twig\Error\RuntimeError | \Twig\Error\SyntaxError $e) {
            echo $e->getMessage();
        }
    }
} else {
    header("location: index.php");
    exit();
}


