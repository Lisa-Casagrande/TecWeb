<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Imposta header 403
http_response_code(403);

//bisogna includere logica navbar (in tutte le pagine)
require_once 'php/navbar.php';

//carica Template HTML
$templatePath = 'html/errore403.html';
if (file_exists($templatePath)) {
    $template = file_get_contents($templatePath);
    
    //sostituisce [navbar] con la variabile generata da navbar.php
    $paginaFinale = str_replace('[navbar]', $navbarBlock, $template);
    
    echo $paginaFinale;
} else {
    die("Errore: Template non trovato.");
}
?>
