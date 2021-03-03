<?php

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

require_once "../includes.php";

$loader = new FilesystemLoader('prosjektleder');
$twig = new Environment($loader);

define('FILENAME_TAG', 'image');

try {
    echo $twig->render('../prosjektleder/layout_prosjektleder.twig');
} catch (\Twig\Error\LoaderError $e) {
} catch (\Twig\Error\RuntimeError $e) {
} catch (\Twig\Error\SyntaxError $e) {
}

