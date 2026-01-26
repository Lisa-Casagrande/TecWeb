<?php
// Nessuno spazio o carattere prima di questo tag!
session_start();

// Svuota completamente l'array di sessione
$_SESSION = array();

// Elimina il cookie di sessione se esiste
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 3600, '/');
}

// Distruggi la sessione
session_destroy();

// Reindirizza alla pagina di login
header("Location: ../login.php");
exit();
?>