<?php

require_once 'includes.php';

//Denne koden er tatt og litt modifisert fra https://codewithawa.com/posts/check-if-user-already-exists-without-submitting-form

if ($request->request->has('username_check')) {
    try {
        $username = $request->request->get('username');
        $stmt = $db->prepare("SELECT count(*) as cntUser FROM Users WHERE username = :username");
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        $stmt->execute();
        $count = $stmt->fetchColumn();
        if($count > 0){
            echo "taken";
        } else {
            echo 'not_taken';
        }
    } catch (Exception $e) {

    }
    exit();
}
if ($request->request->has('email_check')) {
    try {
        $emailAddress = $request->request->get('emailAddress');
        $stmt = $db->prepare("SELECT count(*) as cntUser FROM Users WHERE emailAddress = :emailAddress");
        $stmt->bindParam(':emailAddress', $emailAddress, PDO::PARAM_STR);
        $stmt->execute();
        $count = $stmt->fetchColumn();
        if($count > 0) {
            echo "taken";
        } else {
            echo 'not_taken';
        }
    } catch (Exception $e) {

    }
    exit();
}


?>