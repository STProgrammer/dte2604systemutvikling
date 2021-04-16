<?php


require_once "includes.php";

define('FILENAME_TAG', 'image');

$hourManager = new HourManager($db, $request, $session);
$userManager = new UserManager($db, $request, $session);
$taskManager = new TaskManager($db, $request, $session);

if ($user) {
    $tasks = $taskManager->getAllTasks();
    echo $twig->render('customer_dashboard.twig',
        array('hourManager' => $hourManager,
            'session' => $session, 'user' => $user, 'tasks' => $tasks,
            'TaskManager'=> $taskManager,));
    } else {
    header("location: login.php");
    exit();
}


