<?php

    ini_set('display_errors',1); 
    error_reporting(E_ALL);

function OpenConnection() {

    $dbhost = "mysql1.cs.clemson.edu";
    $dbuser = "CPSC4620_u79r";
    $dbpass = "CPSC4620mariadbpw";
    $db = "CPSC4620_cdtv";

    $mysqli = new mysqli($dbhost, $dbuser, $dbpass, $db) or die("Connection failed: %s\n". $mysqli -> error);

    return $mysqli;
}

function CloseConnection($mysqli) {
    $mysqli -> close();
}

?>
