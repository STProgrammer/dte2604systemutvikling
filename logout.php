<?php
require_once "includes.php";

    //logg out
    $session->clear();

    try {
        echo $twig->render('login.twig', array('session' => $session));
    } catch (LoaderError | RuntimeError | SyntaxError $e) {

    }
?>