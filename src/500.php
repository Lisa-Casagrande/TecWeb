<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Imposta header HTTP 500
http_response_code(500);
//bisogna includere logica navbar (in tutte le pagine)
require_once 'php/navbar.php';

//carica Template HTML
$templatePath = 'html/errore500.html';
if (file_exists($templatePath)) {
    $template = file_get_contents($templatePath);
    
    //sostituisce [navbar] con la variabile generata da navbar.php
    $paginaFinale = str_replace('[navbar]', $navbarBlock, $template);
    
    echo $paginaFinale;
} else {
    die("Errore: Template non trovato.");
}
?>