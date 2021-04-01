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

    $groups = $projectManager->getGroups($projectName);
    $phases = $projectManager->getAllPhases($projectName);
    $groupMembers = $groupManager->getGroupMembers($task->getGroupID());

    $tasksItIsDependentOn = $taskManager->getTasksItIsDependentOn($taskId);
    $dependentTasks = $taskManager->getDependentTasks($taskId);
    $nonDependentTasks = $taskManager->getNonDependentTasks($taskId);

    $parentTask = $taskManager->getTask($task->getParentTask());
    $tasks = $taskManager->getAllTasks(projectName: $projectName);
    $subTasks = $taskManager->getAllTasks(hasSubtask: 0, parentTask: $task->getParentTask());
    if ($request->request->has('task_add') && XsrfProtection::verifyMac("Add task")) {
        if ($taskManager->addMainTask($projectName)) {
            header("Location: ".$request->server->get('REQUEST_URI'));
            exit();
        } else {
            header("Location: ?failedtoaddtasks");
            exit();
        }
    }
    else {
        try {
            echo $twig->render('tasks_editTask.twig', array('session' => $session, 'request' => $request,
                'user' => $user, 'task' => $task, 'tasks' => $tasks, 'group' => $groups, 'groupMembers' => $groupMembers,
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


