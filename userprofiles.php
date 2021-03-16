<?php



require_once "includes.php";

define('FILENAME_TAG', 'image');

$user = $_SESSION['user'];

$UserID = $user->getUserName();

try {
    if (!empty($twig)) {
        echo $twig->render('userprofiles.twig', array('firstName' => $firstName, 'lastName' => $lastName));
    }
} catch (LoaderError | RuntimeError | SyntaxError $e) {
}



