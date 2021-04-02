<?php

    require_once "includes.php";



    if ($user) {
        $hourManager = new HourManager($db, $request, $session);
        $userManager = new UserManager($db, $request, $session);
        $taskManager = new TaskManager($db, $request, $session);
        $hour = $hourManager->getLastHoursForUser($user->getUserId());
        $tasks = $taskManager->getAllTasks();

        $userID = $user->getUserId($user);
        $tasks = $taskManager->getAllTasks();
        $hours = $hourManager->getAllHoursForUser($userID);
        $hourId = $request->query->getInt('hourID');
        $hourWithTask = $hourManager->getAllHoursForUserWithTask($userID);

        if ($user->isAdmin()) {
            try {
                echo $twig->render('admin_dashboard.twig', array('session' => $session, 'user' => $user,
                    'request' => $request));
            } catch (LoaderError | RuntimeError | SyntaxError $e) {
                echo $e->getMessage();
            }
        }
        elseif ($user->isProjectLeader()) {
            try {
                echo $twig->render('projectleader_dashboard.twig', array('session' => $session, 'user' => $user,
                    'request' => $request));
            } catch (LoaderError | RuntimeError | SyntaxError $e) {
                echo $e->getMessage();
            }
        }
        elseif ($user->isGroupLeader()) { //TEAMLEDER
            try {
                echo $twig->render('groupleader_dashboard.twig', array('session' => $session, 'user' => $user,
                    'request' => $request));
            } catch (LoaderError | RuntimeError | SyntaxError $e) {
                echo $e->getMessage();
            }
        }
        elseif ($user->isUser()) { //BRUKER TEMP and WORKER
            try {
                echo $twig->render('employee_dashboard.twig',
                    array('hours' => $hours, 'hour' => $hour, 'hourWithTask' => $hourWithTask,'HourManager' => $hourManager,
                        'UserID' => $userID, 'session' => $session, 'user' => $user, 'tasks' => $tasks));
            } catch (LoaderError | RuntimeError | SyntaxError $e) {
                echo $e->getMessage();
            }
        }
    } else {
        header("location: login.php");
        exit();
    }

?>