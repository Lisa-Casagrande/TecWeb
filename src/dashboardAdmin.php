<?php
// Connessione al database e controllo sicurezza sessione admin
require_once 'php/connessione.php';
require_once 'php/verificaSessioneAdmin.php';

// Includi il template HTML
$templatePath = __DIR__ . '/html/dashboardAdmin.html';
if (file_exists($templatePath)) {
    include $templatePath;
} else {
    die("Errore: template non trovato");
}
?>