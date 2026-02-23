<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Controlla se l'utente è loggato
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

// Controlla se l'utente è admin
if ($_SESSION['user_tipo'] !== 'admin') {
    header("Location: login.php");
    exit();
}