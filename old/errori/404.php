<?php
// Gestione sessione (necessaria se la navbar deve mostrare "Login" o "Profilo")
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


ob_start();
include $_SERVER['DOCUMENT_ROOT'] . '/navbar.php';
$navbar_html = ob_get_clean();


$templatePath = __DIR__ . '/errore404.html';

if (file_exists($templatePath)) {
    $template = file_get_contents($templatePath);
    
    
    $template = str_replace("[navbar]", $navbar_html, $template);
    
    
    header('HTTP/1.1 404 Not Found');
    
    echo $template;
} else {
    die("Errore: Template non trovato.");
}
?>