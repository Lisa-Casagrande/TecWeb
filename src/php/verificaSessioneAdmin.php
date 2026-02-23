<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Non loggato → 401 (non autenticato)
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: /lcasagra/401.php");
    exit();
}

// Loggato ma non admin → 403 (autenticato, ma senza permessi)
if ($_SESSION['user_tipo'] !== 'admin') {
    header("Location: /lcasagra/403.php");
    exit();
}