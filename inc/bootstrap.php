<?php

error_reporting(E_ALL);

ini_set("session.gc_maxlifetime", 3600);
ini_set("session.cookie_lifetime", 3600);

session_start();

if(isset($_COOKIE[session_name()])) {
    setcookie(session_name(), $_COOKIE[session_name()], time() + 3600, '/');
}

?>
