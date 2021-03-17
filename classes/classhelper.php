<?php

function getAll(PDOStatement $stmt, $type, $idAsArrayIndex = false, $execArg = null){
    $ret = array();
    try {
        $stmt->execute($execArg);

        if($idAsArrayIndex){
            while ($obj = $stmt->fetchObject($type)) {
                $ret[$obj->getId()] = $obj;
            }
        } else {
            while ($obj = $stmt->fetchObject($type)) {
                $ret[] = $obj;
            }
        }
    } catch (Exception $e) {
        error($e->getMessage());
    }
    return $ret;
}
function error($errormsg) {
    print "<h2>Something went wrong.. </h2>";
    print "<h4>$errormsg</h4>";
}