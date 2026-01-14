<?php
session_start();

/**
 * Funzioni di utilità per la sessione
 */

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

/**
 * --- CONTROLLI PER PAGINE UTENTE NORMALE ---
 * Deve essere incluso prima di qualsiasi query o output
 */
if (basename($_SERVER['PHP_SELF']) === 'paginaUtente.php') {

    // Se non loggato o tipo utente diverso da 'utente'
    if (!isLoggedIn() || userType() !== 'utente') {

        // Se arriva da un link interno → redirect login
       if (!isset($_SESSION['user_id'])) {
    header('HTTP/1.1 401 Unauthorized');
    include $_SERVER['DOCUMENT_ROOT'] . '/401.php';
    exit();
}
}

function requireUser() {
    if (!isLoggedIn() || userType() !== 'utente') {
        header('HTTP/1.1 401 Unauthorized');
        include $_SERVER['DOCUMENT_ROOT'].'/401.php';
        exit();
    }
}}
?>