<?php

require_once "includes.php";

if ($user) {
    $hourManager = new HourManager($db, $request, $session);
    $userManager = new UserManager($db, $request, $session);
    $reportGenerator = new ReportGenerator($db, $request, $session);
    $userManager = new UserManager($db, $request, $session);
    $users = $reportGenerator->getAllUserStatistics();

    $projects = array();

    if ($user->isAdmin()) {
        $projects = $reportGenerator->getAllProjectsForReport();
    } else {
        $projects = $reportGenerator->getProjectsForUserForReport($user->getUserID());
    }

    if ($user->isAdmin() or $user->isProjectleader()) {


        echo $twig->render('reports.twig',
            array('session' => $session, 'user' => $user, 'users' => $users,
                'projects' => $projects, 'reportGenerator' => $reportGenerator));

    } else if ($user->isGroupLeader() or $user->isEmployee() or $user->isCustomer()) {

        echo $twig->render('reports.twig',
            array('projects' => $projects, 'ProjectManager' => $reportGenerator, 'session' => $session,
                'user' => $user, 'users' => $users));
    } else {
        header("location: index.php");
        exit();
    }
} else {
    header("location: index.php");
    exit();
}
