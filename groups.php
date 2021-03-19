<?php

require_once('includes.php');


$groupManager = new GroupManager($db, $request, $session);

if (!is_null($user)) {
    $groups = $groupManager->getAllGroups();
    try {
        echo $twig->render('groups.twig', array('user' => $user, 'groups' => $groups, 'session' => $session, 'request' => $request));
    } catch (LoaderError | RuntimeError | SyntaxError $e) {  }
}
else {
    header("location: index.php");
    exit();
}