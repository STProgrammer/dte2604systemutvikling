<?php

require_once('includes.php');

$userManager = new UserManager($db, $request, $session);

$userData = array();

if ($request->request->has('register') && XsrfProtection::verifyMac("register")) {
    $userManager->registerUser();
    header("Location: ../?registereduser=1");
    exit();

} else {
    echo $twig->render('user_register.twig', array('script' => $homedir, 'rel' => $rel));
}
?>