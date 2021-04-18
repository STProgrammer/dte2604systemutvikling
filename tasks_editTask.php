<?php

require_once "includes.php";

$projectManager = new ProjectManager($db, $request, $session);
$userManager = new UserManager($db, $request, $session);
$taskManager = new TaskManager($db, $request, $session);
$groupManager = new GroupManager($db, $request, $session);

$task = $taskManager->getTask($request->query->getInt('taskid'));


if (!is_null($user) && !is_null($task) && ($user->isAdmin() or $user->isProjectLeader())) {
    $taskId = $task->getTaskID();
    $projectName = $task->getProjectName();

    $projectId = $projectManager->getProjectByName($projectName);
    $groups = $projectManager->getGroups($projectName);
    $phases = $projectManager->getAllPhases($projectName);
    $groupMembers = $groupManager->getGroupMembers($task->getGroupID());

    $tasksItIsDependentOn = $taskManager->getTasksItIsDependentOn($taskId);
    $dependentTasks = $taskManager->getDependentTasks($taskId);
    $nonDependentTasks = $taskManager->getNonDependentTasks($task);

    $parentTask = $taskManager->getTask($task->getParentTask());
    $tasks = $taskManager->getAllTasks(projectName: $projectName);
    $subTasks = $taskManager->getAllTasks(hasSubtask: 0, parentTask: $taskId);
    if ($request->request->has('task_status_edit') && XsrfProtection::verifyMac("Edit task status")) {
        if ($taskManager->editStatus($taskId)) {
            header("Location: ".$requestUri."&taskstatusedited=1");
            exit();
        } else {
            header("Location: ".$requestUri."&failedtoedittaskstatus=1");
            exit();
        }
    }
    else if ($request->request->has('reestimate') && XsrfProtection::verifyMac("Re-estimate")) {
        if ($taskManager->reEstimate($taskId, $task->getParentTask())) {
            header("Location: ".$requestUri."&taskreestimated=1");
            exit();
        } else {
            header("Location: ".$requestUri."&failedtoreestimatetask=1");
            exit();
        }
    }
    else if ($request->request->has('subtask_add') && XsrfProtection::verifyMac("Add subtask")) {
        if ($taskManager->addSubTask($projectName, $task->getTaskID(), $task->getGroupID())) {
            header("Location: ".$requestUri."&subtaskadded=1");
            exit();
        } else {
            header("Location: ".$requestUri."&failedtoaddsubtask=1");
            exit();
        }
    }
    else if ($request->request->has('group_change') && XsrfProtection::verifyMac("Change group")) {
        if ($taskManager->changeGroup($taskId)) {
            header("Location: ".$requestUri."&groupchanged=1");
            exit();
        } else {
            header("Location: ".$requestUri."&failedtochangegroup=1");
            exit();
        }
    }
    else if (($request->request->has('main_responsible_change') or $request->request->has('phase_change'))
        && XsrfProtection::verifyMac("Change main responsible or phase")) {
        if ($taskManager->editTask($task)) {
            header("Location: ".$requestUri."&taskedited=1");
            exit();
        } else {
            header("Location: ".$requestUri."&failedtoedittask=1");
            exit();
        }
    }
    else if ($request->request->has('add_dependent_tasks') && XsrfProtection::verifyMac("Add dependent tasks")) {
        if ($taskManager->addDependencies($taskId)) {
            header("Location: ".$requestUri."&dependenttasksadded=1");
            exit();
        } else {
            header("Location: ".$requestUri."&failedtoadddependenttasks=1");
            exit();
        }
    }
    else if ($request->request->has('remove_dependent_tasks') && XsrfProtection::verifyMac("Remove dependent tasks")) {
        if ($taskManager->removeDependencies($taskId)) {
            header("Location: ".$requestUri."&dependenttasksremoved=1");
            exit();
        } else {
            header("Location: ".$requestUri."&failedtoremovedependenttasks=1");
            exit();
        }
    }
    else if ($request->request->has('task_delete') && XsrfProtection::verifyMac("Delete task")) {
        if ($taskManager->deleteTask($taskId, $task->getParentTask())) {
            header("Location: projects_editProject.php?projectid=".$projectId."&taskdeleted=1");
            exit();
        } else {
            header("Location: ".$requestUri."&failedtodeletetask=1");
            exit();
        }
    }
    else {
        try {
            echo $twig->render('tasks_editTask.twig', array('session' => $session, 'request' => $request,
                'user' => $user, 'task' => $task, 'tasks' => $tasks, 'groups' => $groups, 'groupMembers' => $groupMembers,
                'phases' => $phases, 'subTasks' => $subTasks, 'tasksItIsDependentOn' => $tasksItIsDependentOn,
                'dependentTasks' => $dependentTasks, 'nonDependentTasks' => $nonDependentTasks));
        } catch (\Twig\Error\LoaderError | \Twig\Error\RuntimeError | \Twig\Error\SyntaxError $e) {
            echo $e->getMessage();
        }
    }
} else {
    header("location: index.php");
    exit();
}


