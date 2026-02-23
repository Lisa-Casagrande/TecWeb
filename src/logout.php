<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Distrugge la sessione
$_SESSION = array();

// Cancella il cookie di sessione
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time()-3600, '/');
}

session_destroy();

// Redirect alla home
header("Location: login.php");
exit;
?>