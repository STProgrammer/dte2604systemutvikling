<?php


require_once "includes.php";

define('FILENAME_TAG', 'image');

$hourManager = new HourManager($db, $request, $session);
$userManager = new UserManager($db, $request, $session);
$taskManager = new TaskManager($db, $request, $session);


if ($user) {
    $userID = $user->getUserId($user);
    $tasks = $taskManager->getAllTasks();

    $hour = $hourManager->getHour($userID);
    $hours = $hourManager->getHours(whoWorked: $userID);
    $hoursAll = $hourManager->getAllHours();

    if ($request->request->has('edit_comment_hour') && XsrfProtection::verifyMac("Edit Comment")) {
        $hourID = $request->query->getInt('hourId');
        if ($hourManager->editComment($hourID)) {
            header("Location: " . $request->server->get('REQUEST_URI'));
            exit();
        } else {
            header("Location: " . $request->server->get('REQUEST_URI') . "&failedtaddphase=!");
            exit();
        }
    } elseif ($user->isAdmin() && $request->request->has('edit_commentBoss_hour') && XsrfProtection::verifyMac("Edit Comment Boss")) {
        $hourID = $request->query->getInt('hourId');
        if ($hourManager->editCommentBoss($hourID)) {
            header("Location: ".$request->server->get('REQUEST_URI'));
            exit();
        } else {
            header("Location: ".$request->server->get('REQUEST_URI')."&failedtaddphase=!");
            exit();
        }
    } else {
        echo $twig->render('timeregistrations.twig',
            array('session' => $session, 'request' => $request, 'user' => $user,
                'hours' => $hours, 'hour' => $hour, 'hoursAll' => $hoursAll,
                'hourManager' => $hourManager,
                'userID' => $userID,
                'tasks' => $tasks));

    }
} else {
    header("location: login.php");
    exit();
}
