<?php

require_once "includes.php";




if (!empty($twig)) {
    try {
        echo $twig->render('admin_dashboard.twig');
    } catch (LoaderError | SyntaxError | RuntimeError $e) {
        echo $e->getMessage();
    }
}
