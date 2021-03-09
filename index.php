<?php

    //Håndterer login
    require_once "login.php";



/* Denne Twig funksjonen er tatt fra https://stackoverflow.com/questions/61407758/how-to-change-one-value-in-get-by-clicking-a-link-or-button-from-twig-with/61407993#61407993 */
    $twig->addFunction(new \Twig\TwigFunction('get_page_url', function($query = [], $append = true) {
    $tmp = $append ? $_GET : [];
    foreach($query as $key => $value) $tmp[$key] = $value;

    return '?' . http_build_query($tmp);
    }));

    echo $twig->render('index.twig', array('user' => $user,
        'session' => $session, 'rel' => $rel));

?>