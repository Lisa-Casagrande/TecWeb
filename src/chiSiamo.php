<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'php/connessione.php';
require_once 'php/navbar.php';

//carica Template HTML
$templatePath = 'html/chiSiamo.html';
if (file_exists($templatePath)) {
    $template = file_get_contents($templatePath);
    $paginaFinale = str_replace('[navbar]', $navbarBlock, $template);
    
    echo $paginaFinale;
} else {
    die("Errore: Template non trovato.");
}
?>
