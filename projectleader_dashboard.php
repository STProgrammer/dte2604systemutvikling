<?php

use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

require_once "includes.php";

define('FILENAME_TAG', 'image');


if (!empty($twig)) {
    try {
        echo $twig->render('projectleader_dashboard.twig');
    } catch (LoaderError | SyntaxError | RuntimeError $e) {
    }
}


