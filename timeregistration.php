<?php


use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

require_once "includes.php";

define('FILENAME_TAG', 'image');


try {
    echo $twig->render('timeregistrations.twig');
} catch (LoaderError $e) {
} catch (RuntimeError $e) {
} catch (SyntaxError $e) {
}


