<?php
session_start();

// Controlla se l'utente è loggato e se è admin
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
   header('HTTP/1.1 401 Unauthorized');
        include $_SERVER['DOCUMENT_ROOT'].'/401.php';
        exit();
}

// Controlla se l'utente non è admin → 403 Forbidden
if ($_SESSION['user_tipo'] !== 'admin') {
    header('HTTP/1.1 403 Forbidden');
        include $_SERVER['DOCUMENT_ROOT'].'/403.php';
        exit();
}
?>