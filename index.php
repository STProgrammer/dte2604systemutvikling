<?php

    require_once "includes.php";
    $user = $session->get('User');
    $hourManager = new HourManager($db, $request, $session);
    $userManager = new UserManager($db, $request, $session);
    $taskManager = new TaskManager($db, $request, $session);
    $userID = $user->getUserId($user);
    $hour = $hourManager->getLastHoursForUser($userID);
    $tasks = $taskManager->getAllTasks();

    if ($user) {
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
                echo $twig->render('employee_dashboard.twig', array('Hour' => $hour, 'HourManager' => $hourManager,
                    'UserID' => $userID, 'session' => $session, 'user' => $user,
                    'request' => $request, 'tasks' => $tasks));
            } catch (LoaderError | RuntimeError | SyntaxError $e) {
                echo $e->getMessage();
            }
        }
    } else {
        header("location: login.php");
        exit();
    }

?>