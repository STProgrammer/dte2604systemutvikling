<?php

require_once "includes.php";

define('FILENAME_TAG', 'image');


if (!empty($twig)) {
    try {
        echo $twig->render('error.twig');
    } catch (LoaderError | SyntaxError | RuntimeError $e) {
    }
}