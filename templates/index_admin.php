<?php

require_once "../includes.php";

define('FILENAME_TAG', 'image');


try {
    echo $twig->render('prosjekter_admin.twig');
} catch (\Twig\Error\LoaderError $e) {
} catch (\Twig\Error\RuntimeError $e) {
} catch (\Twig\Error\SyntaxError $e) {
}

?>
