<?php


require_once "includes.php";


$hourManager = new HourManager($db, $request, $session);
$userManager = new UserManager($db, $request, $session);
$taskManager = new TaskManager($db, $request, $session);


if ($user) {
    $userID = $user->getUserId($user);

    $tasks = $taskManager->getAllTasks(); //trenger vi den?

    $hours = $hourManager->getHours(whoWorked: $userID);
    $hourID = $request->query->getInt('hourID');
    $hour = $hourManager->getHour($hourID);

    if ($request->request->has('edit_comment_hour') && XsrfProtection::verifyMac("Edit Comment")) {
        if ($hourManager->editComment($hourID)) {
            header("Location: ".$requestUri."&comment=1");
            exit();
        } else {
            header("Location: ".$requestUri."&failedtocomment=1");
            exit();
        }
    }elseif ($request->request->has('edit_commentBoss_hour') && XsrfProtection::verifyMac("Edit Comment Boss")) {
        if ($hourManager->editCommentBoss($hourID)) {
            header("Location: ".$requestUri."&commentbyboss=1");
            exit();
        } else {
            header("Location: ".$requestUri."&failedtocommentbyboss=1");
            exit();
        }
    }else {
        echo $twig->render('timeregistrationsEdit.twig',
                array('hour' => $hour, 'hours' => $hours, 'HourManager' => $hourManager, 'UserID' => $userID, 'session' => $session,
                    'user' => $user, 'tasks' => $tasks, 'hourID' => $hourID));
    }

} else {
    header("location: login.php");
    exit();
}
