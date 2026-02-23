<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Controlla se l'utente è loggato
function isLoggedIn() {
    return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
}

// Restituisce il tipo di utente ('utente', 'admin', ecc.)
function userType() {
    return $_SESSION['user_tipo'] ?? null;
}

// Restituisce l'ID dell'utente loggato
function userId() {
    return $_SESSION['user_id'] ?? null;
}

// Richiede che l'utente sia loggato come 'utente', altrimenti redirect al login
function requireUser() {
    if (!isLoggedIn() || userType() !== 'utente') {
        header("Location: login.php");
        exit();
    }
}