<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

ob_start();
include $_SERVER['DOCUMENT_ROOT'] . '/navbar.php';
$navbar_html = ob_get_clean();

$templatePath = __DIR__ . '/html/errore401.html';

if (file_exists($templatePath)) {
    $template = file_get_contents($templatePath);
    
    $template = str_replace("[navbar]", $navbar_html, $template);
    
    echo $template;
} else {
    die("Errore: Template non trovato.");
}
?>

