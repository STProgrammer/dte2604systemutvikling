<?php

require_once('includes.php');


//Denne siden skal bare sees av admin eller prosjektleder og viser timer brukt av en enkelt bruker på et spesifikt prosjekt

$userManager = new UserManager($db, $request, $session);
$user = $session->get('User');

if ($user && ($user->isAdmin() | $user->isProjectLeader())) {
    $userID = $request->query->getInt('userID');
    $userToView = $userManager->getUser($userID);



    try {
        echo $twig->render('user_contribution.twig', array('session' => $session,
            'request' => $request, 'user' => $user, 'userToView' => $userToView, 'userID' => $userID));
    } catch (LoaderError | \Twig\Error\RuntimeError | \Twig\Error\SyntaxError $e) { echo $e->getMessage(); }


} else {
    header("location: index.php");
    exit();
}
