<?php

require_once('includes.php');

$reguser = new UserManager($db, $request, $session);

$userData = array();

if ($request->request->has('register') && XsrfProtection::verifyMac("Register")) {
    $reguser->registerUser();
    header("Location: ../?registereduser=1");
    exit();

} else {
    echo $twig->render('register.twig', array('script' => $homedir, 'rel' => $rel, 'user' => $user));
}
?>