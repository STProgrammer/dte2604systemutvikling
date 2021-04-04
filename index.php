<?php

    require_once "includes.php";



    if ($user) {
        $hourManager = new HourManager($db, $request, $session);
        $userManager = new UserManager($db, $request, $session);
        $taskManager = new TaskManager($db, $request, $session);

        $userID = $user->getUserId($user);
        $users = $userManager->getAllUsers('dateRegistered');

        $hoursAll = $hourManager->getAllHours();
        $hours = $hourManager->getAllHoursForUser($userID);
        $hourWithTask = $hourManager->getAllHoursForUserWithTask($userID);

        $tasks = $taskManager->getAllTasks();

        if ($user->isAdmin()) {
            try {
                echo $twig->render('admin_dashboard.twig',
                    array( 'session' => $session, 'request' => $request,
                        'user' => $user, 'users' => $users,
                        'hours' => $hours, 'hourWithTask' => $hourWithTask,
                        'hoursAll' => $hoursAll, 'HourManager' => $hourManager,
                        'tasks' => $tasks));
            } catch (LoaderError | RuntimeError | SyntaxError $e) {
                echo $e->getMessage();
            }
        }
        elseif ($user->isProjectLeader()) {
            try {
                echo $twig->render('projectleader_dashboard.twig',
                    array( 'session' => $session, 'request' => $request,
                        'user' => $user, 'users' => $users,
                        'hours' => $hours, 'hourWithTask' => $hourWithTask,
                        'hoursAll' => $hoursAll, 'HourManager' => $hourManager,
                        'tasks' => $tasks));
            } catch (LoaderError | RuntimeError | SyntaxError $e) {
                echo $e->getMessage();
            }
        }
        elseif ($user->isGroupLeader()) { //TEAMLEDER
            try {
                echo $twig->render('groupleader_dashboard.twig',
                    array( 'session' => $session, 'request' => $request,
                        'user' => $user, 'users' => $users,
                        'hours' => $hours, 'hourWithTask' => $hourWithTask,
                        'hoursAll' => $hoursAll, 'HourManager' => $hourManager,
                        'tasks' => $tasks));
            } catch (LoaderError | RuntimeError | SyntaxError $e) {
                echo $e->getMessage();
            }
        }
        elseif ($user->isUser()) { //BRUKER TEMP and WORKER
            try {
                echo $twig->render('employee_dashboard.twig',
                    array( 'session' => $session, 'request' => $request,
                        'user' => $user, 'users' => $users,
                        'hours' => $hours, 'hourWithTask' => $hourWithTask,
                        'hoursAll' => $hoursAll, 'HourManager' => $hourManager,
                        'tasks' => $tasks));
            } catch (LoaderError | RuntimeError | SyntaxError $e) {
                echo $e->getMessage();
            }
        }
    } else {
        header("location: login.php");
        exit();
    }

?>