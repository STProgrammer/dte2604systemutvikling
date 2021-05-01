<?php

require_once "includes.php";

if ($user->isAdmin()) {
    $hourID = $request->query->getInt('hourId');
    $hourManager = new HourManager($db, $request, $session);
    if($hourManager->verifyHour($hourID)) {
        header("location: timeregistration.php");
    }
}
