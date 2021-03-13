<?php

require_once "includes.php";

define('FILENAME_TAG', 'image');


if (!empty($twig)) {
    echo $twig->render('projectleader_dashboard.twig');
}
else {
    echo $twig->render('Error Twig');
}

?>


