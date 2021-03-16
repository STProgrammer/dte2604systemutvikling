<?php

require_once "includes.php";

//logg out
if ($request->request->has('logout') && XsrfProtection::verifyMac("logout")) {
    $session->clear();
    $referer = $request->server->get('HTTP_REFERER');
    header('location: '.$referer);
    exit();
}

// if login submitted
elseif ($request->request->has('login')) {
    if(XsrfProtection::verifyMac("login") && User::login($db, $request, $session)) {
        $user = $session->get('User');
        if ($session->get('loggedin') && $user->verifyUser($request)) {
            $referer = $request->server->get('HTTP_REFERER');
            header('location: '.$referer);
            exit();
        }
    } //if login submitted but failed to login
    else {
        $get_info = "?loginfail=1";
        header("Location: ".$get_info);
        exit();
    }
}

// if logged in
elseif ($user) {
    try {
        echo $twig->render('admin_dashboard.twig', array('session' => $session));
    } catch (LoaderError | RuntimeError | SyntaxError $e) {
    }

}

//just ready to login
else {
    try {
        echo $twig->render('login.twig', array('session' => $session));
    } catch (LoaderError | RuntimeError | SyntaxError $e) {
    }

}

?>