<?php

require_once "includes.php";



if ($user) {

    if ($user->isAdmin()) {
        header("location: admin_dashboard.php");
    }

    elseif ($user->isProjectLeader()) {
        header("location: projectleader_dashboard.php");

    }

    elseif ($user->isCustomer()) {
        header("location: customer_dashboard.php");
    }

    elseif ($user->isGroupLeader()) { //TEAMLEDER
        header("location: groupleader_dashboard.php");

    }

    elseif ($user->isUser()) { //BRUKER TEMP and WORKER
        header("location: employee_dashboard.php");
    }
} else {
    header("location: login.php");
    exit();
}

?>