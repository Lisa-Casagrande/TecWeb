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

// Richiede che l'utente sia loggato (qualsiasi tipo), altrimenti → 401
function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: /lcasagra/401.php");
        exit();
    }
}

// Richiede che l'utente sia loggato come 'utente' (non admin), altrimenti:
// - non loggato → 401
// - loggato ma tipo sbagliato → 403
function requireUser() {
    if (!isLoggedIn()) {
        header("Location: /lcasagra/401.php");
        exit();
    }
    if (userType() !== 'utente') {
        header("Location: /lcasagra/403.php");
        exit();
    }
}