<?php
require_once 'php/connessione.php';
require_once 'php/verificaSessioneAdmin.php';

try {
    $stmt = $pdo->query("SELECT id_base, nome FROM base ORDER BY nome ASC");
    $basi = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Errore nel recupero delle basi: " . $e->getMessage());
}

$templatePath = __DIR__ . '/html/aggiungiProdotto.html';

if (file_exists($templatePath)) {
    $template = file_get_contents($templatePath);
    
    $opzioni_basi = '';
    foreach ($basi as $base) {
        $opzioni_basi .= '<option value="' . $base['id_base'] . '">';
        $opzioni_basi .= htmlspecialchars($base['nome']);
        $opzioni_basi .= '</option>' . "\n";
    }
    
    $template = str_replace('[OPZIONI_BASI]', $opzioni_basi, $template);
    
    echo $template;
} else {
    die("Errore: Template non trovato.");
}
?>