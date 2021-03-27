<?php

require_once('includes.php');


//Denne siden skal bare sees av admin eller prosjektleder og viser timer brukt av en enkelt bruker på et spesifikt prosjekt

$userManager = new UserManager($db, $request, $session);
$user = $session->get('user');
$userToView = $userManager->getUser($request->query->getInt('userid'));

if ($user && ($user->isAdmin() | $user->isProjectLeader())) {
    $userToView = $userManager->getUser($request->query->getInt('userid'));


    try {
        echo $twig->render('user_contribution.twig', array('session' => $session,
            'request' => $request, 'user' => $user, 'userToView' => $userToView));
    } catch (LoaderError | \Twig\Error\RuntimeError | \Twig\Error\SyntaxError $e) { echo $e->getMessage(); }


} else {
    try {
        echo $twig->render('user_contribution.twig', array('session' => $session,
            'request' => $request, 'user' => $user, 'userToView' => $userToView));
    } catch (LoaderError | \Twig\Error\RuntimeError | \Twig\Error\SyntaxError $e) { echo $e->getMessage(); }
}
