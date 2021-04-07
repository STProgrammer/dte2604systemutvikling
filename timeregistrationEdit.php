<?php


require_once "includes.php";

define('FILENAME_TAG', 'image');

$user = $session->get('User');


$hourManager = new HourManager($db, $request, $session);
$userManager = new UserManager($db, $request, $session);
$taskManager = new TaskManager($db, $request, $session);
$tasks = $taskManager->getAllTasks();


if ($user) {
    $userID = $user->getUserId($user);
    $hours = $hourManager->getAllHoursForUser($userID);
    $hourID = $request->query->getInt('hourID');
    $hour = $hourManager->getHour($hourID);

    if ($request->request->has('edit_comment_hour') && XsrfProtection::verifyMac("Edit Comment")) {
        if ($hourManager->editComment($hourID)) {
            header("Location: ".$request->server->get('REQUEST_URI'));
            exit();
        } else {
            header("Location: ".$request->server->get('REQUEST_URI')."&failedtaddphase=!");
            exit();
        }
    }elseif ($request->request->has('edit_commentBoss_hour') && XsrfProtection::verifyMac("Edit Comment Boss")) {
        if ($hourManager->editCommentBoss($hourID)) {
            header("Location: ".$request->server->get('REQUEST_URI'));
            exit();
        } else {
            header("Location: ".$request->server->get('REQUEST_URI')."&failedtaddphase=!");
            exit();
        }
    }else {
        echo $twig->render('timeregistrationsEdit.twig',
                array('hour' => $hour, 'Hours' => $hours, 'HourManager' => $hourManager, 'UserID' => $userID, 'session' => $session,
                    'user' => $user, 'tasks' => $tasks, 'hourID' => $hourID));
    }

} else {
    header("location: login.php");
    exit();
}
