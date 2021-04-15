<?php

require_once "includes.php";



if ($user) {
    $hourManager = new HourManager($db, $request, $session);
    $userManager = new UserManager($db, $request, $session);
    $taskManager = new TaskManager($db, $request, $session);

    $userID = $user->getUserId($user);
    $users = $userManager->getAllUsers('dateRegistered');

    $hours = $hourManager->getAllHours();
    $categories = $taskManager->getCategories();
    $tasks = $taskManager->getAllTasks();

    if ($user->isAdmin()) {
        try {
            echo $twig->render('admin_dashboard.twig',
                array( 'session' => $session, 'request' => $request,
                    'user' => $user, 'users' => $users,
                    'hours' => $hours, 'HourManager' => $hourManager,
                    'tasks' => $tasks));
        } catch (LoaderError | RuntimeError | SyntaxError $e) {
            echo $e->getMessage();
        }
    }
    elseif ($user->isProjectLeader()) {
        header("location: projectleader_dashboard.php");

    }
    elseif ($user->isGroupLeader()) { //TEAMLEDER
        header("location: groupleader_dashboard.php");

    }
    elseif ($user->isUser()) { //BRUKER TEMP and WORKER
        header("location: employee_dashboard.php");
    }
} else {
    header("location: login.php");
    exit();
}

?>