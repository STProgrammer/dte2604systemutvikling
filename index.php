<?php

    require_once "includes.php";


    if ($user) {
        if ($user->isAdmin()) {
            try {
                echo $twig->render('admin_dashboard.twig', array('session' => $session, 'user' => $user,
                    'request' => $request));
            } catch (LoaderError | RuntimeError | SyntaxError $e) {  }
        }
        elseif ($user->isProjectLeader()) {
            try {
                echo $twig->render('projectleader_dashboard.twig', array('session' => $session, 'user' => $user,
                    'request' => $request));
            } catch (LoaderError | RuntimeError | SyntaxError $e) {  }
        }
        elseif ($user->isGroupLeader()) { //TEAMLEDER
            try {
                echo $twig->render('groupleader_dashboard.twig', array('session' => $session, 'user' => $user,
                    'request' => $request));
            } catch (LoaderError | RuntimeError | SyntaxError $e) {  }
        }
        elseif ($user->isUser()) { //BRUKER TEMP and WORKER
            try {
                echo $twig->render('timeregistrations.twig', array('session' => $session, 'user' => $user,
                    'request' => $request));
            } catch (LoaderError | RuntimeError | SyntaxError $e) {  }
        }
    } else {
        header("location: login.php");
        exit();
    }

?>