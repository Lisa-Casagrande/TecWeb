<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Imposta header 404
http_response_code(404);

require_once 'php/navbar.php';

$templatePath = 'html/errore404.html';
if (file_exists($templatePath)) {
    $template = file_get_contents($templatePath);
    
    // Sostituisci il placeholder navbar
    $paginaFinale = str_replace('[navbar]', $navbarBlock, $template);
    
    echo $paginaFinale;
} else {
    // logga errore invece di morire
    error_log("Template 404 non trovato: $templatePath");
    echo "<h1>Errore 404</h1><p>La pagina richiesta non esiste.</p>";
}
?>