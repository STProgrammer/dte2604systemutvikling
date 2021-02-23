<?php

$host = "kark.uit.no";
$dbname = "";
$username = "";
$password =  "dinjævell";

try
{
    $db = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
}
catch(PDOException $e)
{
    //throw new Exception($e->getMessage(), $e->getCode);
    print($e->getMessage());
}
?>