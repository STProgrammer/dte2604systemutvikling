<?php

require_once('includes.php');


//Denne siden skal bare sees av admin eller prosjektleder og viser timer brukt av en enkelt bruker på et spesifikt prosjekt

$userManager = new UserManager($db, $request, $session);
$hourManager = new HourManager($db, $request, $session);
$taskManager = new TaskManager($db, $request, $session);

$user = $session->get('User');

if ($user && ($user->isAdmin() | $user->isProjectLeader())) {
    $userID = $request->query->getInt('userID');
    $userToView = $userManager->getUser($userID);
    $hours = $hourManager->getAllHoursForUser($userID);
    $hour = $hourManager->getHour($userID);
    $hourWithTask = $hourManager->getAllHoursForUserWithTask($userID);
    $tasks = $taskManager->getAllTasks();

    if ($request->request->has('edit_commentBoss_hour') && XsrfProtection::verifyMac("Edit Comment Boss")) {
        if ($hourManager->editCommentBoss($hour)) {
            header("Location: ".$request->server->get('REQUEST_URI'));
            exit();
        } else {
            header("Location: ".$request->server->get('REQUEST_URI')."&failedtaddphase=!");
            exit();
        }
    }
    else {
        echo $twig->render('user_contribution.twig',
            array('session' => $session, 'request' => $request, 'user' => $user,
                'userToView' => $userToView, 'userID' => $userID,
                'hour' => $hour, 'hours' => $hours, 'hourWithTask' => $hourWithTask,
                'tasks' => $tasks));

    }
} else {
    header("location: index.php");
    exit();
}
