<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'php/connessione.php';

ob_start();
include 'navbar.php';
$navbar_html = ob_get_clean();

$templatePath = __DIR__ . '/html/chiSiamo.html';

if (file_exists($templatePath)) {
    $template = file_get_contents($templatePath);
    
    $template = str_replace('[NAVBAR]', $navbar_html, $template);
    
    echo $template;
} else {
    die("Errore: Template non trovato.");
}
?>