<?php

require_once "includes.php";




if (!empty($twig)) {
    try {
        echo $twig->render('error.twig');
    } catch (LoaderError | SyntaxError | RuntimeError $e) {
        echo $e->getMessage();
    }
}