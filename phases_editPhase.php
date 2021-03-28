<?php

require_once "includes.php";

$projectManager = new ProjectManager($db, $request, $session);
$userManager = new UserManager($db, $request, $session);

$phase = $projectManager->getPhase($request->query->getInt('phaseid'));


if (!is_null($user) && !is_null($phase) && ($user->isAdmin() or $user->isProjectLeader())) {
    $project = $projectManager->getProjectByName($phase->getProjectName());
    $projectName =  $phase->getProjectName();
    $phaseId = $phase->getPhaseID();
    $employees = $userManager->getAllEmployees("firstName"); //alle arbeidere
    $phases = $projectManager->getAllPhases($projectName);
    $members = $projectManager->getProjectMembers($project->getProjectName());
    if ($request->request->has('phase_edit') && XsrfProtection::verifyMac("Phase edit")) {
        if ($projectManager->editPhase($phase, $project)) {
            header("Location: ".$request->server->get('REQUEST_URI'));
            exit();
        } else {
            header("Location: ".$request->server->get('REQUEST_URI')."?failedtoeditphase");
            exit();
        }
    }
    else if ($request->request->has('task_add') && $user->isAdmin()) {
        if (!$user->isAdmin()) {
            $request->request->set('isAdmin', 0);
        }
        if ($projectManager->addGroup($project->getProjectName()) && XsrfProtection::verifyMac("Add task")) {
            header("Location: ".$request->server->get('REQUEST_URI'));
            exit();
        } else {
            header("Location: ?failedtoaddtasks");
            exit();
        }
    }
    else if ($request->request->has('remove_task') && $user->isAdmin()) {
        if ($projectManager->removeEmployees($project) && XsrfProtection::verifyMac("Remove tasks")) {
            header("Location: ".$request->server->get('REQUEST_URI'));
            exit();
        } else {
            header("Location: ?failedtoremovetasks");
            exit();
        }
    }
    else if ($request->request->has('phase_delete') && $user->isAdmin()) {
        if ($projectManager->deletePhase($phaseId) && XsrfProtection::verifyMac("Delete phase")) {
            header("Location: ".$request->server->get('REQUEST_URI'));
            exit();
        } else {
            header("Location: ".$request->server->get('REQUEST_URI')."?failedtodeleteprojects");
            exit();
        }
    }
    else {
        try {
            echo $twig->render('phases_editPhase.twig',
                array('session' => $session, 'request' => $request, 'user' => $user,
                    'project' => $project,  'members' => $members,
                    'employees' => $employees, 'phase' => $phase,
                'phases' => $phases));
        } catch (\Twig\Error\LoaderError | \Twig\Error\RuntimeError | \Twig\Error\SyntaxError $e) {
            echo $e->getMessage();
        }
    }
} else {
    header("location: index.php");
    exit();
}


