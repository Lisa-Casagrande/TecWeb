<?php
if (session_status() === PHP_SESSION_NONE) { 
    session_start(); 
}
require_once 'php/connessione.php';

// Variabili per il template
$basi = [];
$ingredientiPerTipo = [];
$erroreDatiMancanti = false;

try {
    // Query per ottenere le basi
    $sqlBasi = "SELECT * FROM base ORDER BY nome";
    $stmtBasi = $pdo->query($sqlBasi);
    $basi = $stmtBasi->fetchAll();
    
    // Query per ottenere gli ingredienti
    $sqlIngredienti = "SELECT * FROM ingrediente ORDER BY tipo, nome";
    $stmtIngredienti = $pdo->query($sqlIngredienti);
    $ingredientiRaw = $stmtIngredienti->fetchAll();
    
    // Organizza ingredienti per tipo
    foreach ($ingredientiRaw as $ing) {
        $tipo = $ing['tipo'];
        if (!isset($ingredientiPerTipo[$tipo])) { 
            $ingredientiPerTipo[$tipo] = []; 
        }
        $ingredientiPerTipo[$tipo][] = $ing;
    }
    
} catch (PDOException $e) {
    error_log("Errore creaBlend.php: " . $e->getMessage());
    $erroreDatiMancanti = true;
}

// Funzione helper per ottenere il titolo del tipo di ingrediente
function getTitoloTipo($tipo) {
    switch ($tipo) {
        case 'frutto': 
            return 'Frutti e Bacche';
        case 'spezia': 
            return 'Spezie e Radici';
        case 'fiore': 
            return 'Fiori e Erbe';
        case 'dolcificante': 
            return 'Dolcificanti Naturali';
        case 'note': 
            return 'Note Particolari';
        default: 
            return ucfirst($tipo);
    }
}

// Includi il template HTML
$templatePath = __DIR__ . '/html/creaBlend.html';
if (file_exists($templatePath)) {
    include $templatePath;
} else {
    die("Errore: template non trovato");
}
?>