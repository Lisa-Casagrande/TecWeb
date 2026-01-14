<?php
// Connessione al DB
require_once 'php/connessione.php';
require_once 'php/verificaSessioneAdmin.php';

// Variabili per il template
$ordini = [];
$ordiniHtml = '';

// Query che recupera ordini e nome utente, ordinati per data decrescente (dal più recente)
$sql = "SELECT ordine.*, utente.nome, utente.cognome 
        FROM ordine 
        JOIN utente ON ordine.id_utente = utente.id_utente 
        ORDER BY data_ordine DESC";

try {
    $ordini = $pdo->query($sql)->fetchAll();
    
    // Genera HTML per gli ordini
    foreach ($ordini as $ordine) {
        $stato = $ordine['stato_ord'];
        
        // Mappa Stati -> Classi CSS
        $mappaStati = [
            'annullato'       => 'stato-rosso',
            'in_attesa'       => 'stato-giallo',
            'in_preparazione' => 'stato-giallo',
            'pagato'          => 'stato-verde',
            'spedito'         => 'stato-verde',
            'consegnato'      => 'stato-blu'
        ];
        
        $classStato = isset($mappaStati[$stato]) ? $mappaStati[$stato] : 'stato-giallo';
        $statoFormattato = ucfirst(str_replace('_', ' ', $stato));
        
        $ordiniHtml .= '
            <article class="order-card">
                <div class="order-info">
                    <h3 class="order-number">Ordine #' . $ordine['id_ordine'] . '</h3>
                    <div class="order-meta">
                        <p><strong>Data:</strong> ' . date("d/m/Y H:i", strtotime($ordine['data_ordine'])) . '</p>
                        <p><strong>Cliente:</strong> ' . htmlspecialchars($ordine['nome'] . " " . $ordine['cognome']) . '</p>
                    </div>
                </div>

                <div class="order-price">
                    <p>Totale</p>
                    <p>€ ' . number_format($ordine['totale'], 2) . '</p>
                </div>

                <div class="order-status-action">
                    <div class="status-wrapper">
                        <span class="' . $classStato . '"></span>
                        <span>' . $statoFormattato . '</span>
                    </div>
                    <a href="dettaglioOrdineAdmin.php?id=' . $ordine['id_ordine'] . '" class="bottone-primario">Vedi dettaglio</a>
                </div>
            </article>
        ';
    }
    
} catch (PDOException $e) {
    die("Errore recupero ordini: " . $e->getMessage());
}

// Se non ci sono ordini
if (empty($ordiniHtml)) {
    $ordiniHtml = '<p style="text-align:center; padding: 20px;">Nessun ordine presente nel sistema.</p>';
}

// Includi il template HTML
$templatePath = __DIR__ . '/html/gestioneOrdini.html';
if (file_exists($templatePath)) {
    include $templatePath;
} else {
    die("Errore: template non trovato");
}
?>