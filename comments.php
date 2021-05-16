<?php

require_once "includes.php";


if (!is_null($user)) {
    if (!isset($db)) {
        echo $twig->render('error.twig', array('msg' => 'No database connected!'));
    }
    if (empty($twig)) {
        echo $twig->render('error.twig', array('msg' => 'Twig not working!'));
    }

    $HourManager = new CommentsManager($db, $request, $session);
    $hours = $HourManager->getAllHours();

    $user = $session->get('User');

    $userManager = new UserManager($db, $request, $session);
    $users = $userManager->getAllUsers("lastName");

    $TaskManager = new TaskManager($db, $request, $session);
    $tasks = $TaskManager->getAllTasks();

    echo $twig->render('comments.twig',
        array('session' => $session, 'hours' => $hours, 'user' => $user, 'users' => $users, 'tasks' => $tasks));
} else {
    header("location: index.php");
    exit();
}


