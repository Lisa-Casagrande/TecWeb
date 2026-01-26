<?php
require_once 'php/verificaSessione.php';
requireUser();

// ===== GESTIONE LOGOUT =====
if (isset($_GET['azione']) && $_GET['azione'] === 'logout') {
    $_SESSION = array();
    if (isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time()-3600, '/');
    }
    session_destroy();
    header("Location: login.php");
    exit();
}
// ===========================

require_once 'php/connessione.php';
require_once 'php/navbar.php';

try {
    $userId = userId();

    // Dati utente
    $stmt = $pdo->prepare("
        SELECT id_utente, nome, cognome, email, data_registrazione, citta, indirizzo, cap
        FROM utente
        WHERE id_utente = :id
        LIMIT 1");
    $stmt->execute([':id' => $userId]);
    $utente = $stmt->fetch();

    // Ultimi 5 ordini
    $stmt_ordini = $pdo->prepare("
        SELECT id_ordine, data_ordine, stato_ord, totale
        FROM ordine
        WHERE id_utente = :id
        ORDER BY data_ordine DESC
        LIMIT 5
    ");
    $stmt_ordini->execute([':id' => $userId]);
    $ordini = $stmt_ordini->fetchAll();

} catch (PDOException $e) {
    die("Errore caricamento dati: " . $e->getMessage());
}

// Mappa per gli stati degli ordini
$mappaStati = [
    'in_attesa' => 'stato-giallo',
    'confermato' => 'stato-verde',
    'spedito' => 'stato-verde',
    'consegnato' => 'stato-verde',
    'annullato' => 'stato-rosso'
];

// Genera dati utente per il template
$nomeCompleto = htmlspecialchars($utente['nome'] . ' ' . $utente['cognome']);
$emailUtente = htmlspecialchars($utente['email']);
$dataRegistrazione = date("d/m/Y", strtotime($utente['data_registrazione']));

// Dati indirizzo
$citta = htmlspecialchars($utente['citta'] ?? '');
$via = htmlspecialchars($utente['indirizzo'] ?? '');
$cap = htmlspecialchars($utente['cap'] ?? '');
$indirizzoCompleto = trim("$via, $cap $citta");

// Genera HTML ordini
$ordiniHTML = '';
if (!empty($ordini)) {
    foreach ($ordini as $ordine) {
        $stato = $ordine['stato_ord'];
        $classStato = isset($mappaStati[$stato]) ? $mappaStati[$stato] : 'stato-giallo';
        $statoFormattato = ucfirst(str_replace('_', ' ', $stato));
        $dataOrdine = date("d/m/Y", strtotime($ordine['data_ordine']));
        $totaleOrdine = number_format($ordine['totale'], 2);
        
        $ordiniHTML .= '
        <div class="order-card">
            <div class="order-info">
                <h3 class="order-number">Ordine #' . $ordine['id_ordine'] . '</h3>
                <div class="order-meta">
                    <p><strong>Data:</strong> ' . $dataOrdine . '</p>
                </div>
            </div>

            <div class="order-price">
                <p>Totale</p>
                <p>€ ' . $totaleOrdine . '</p>
            </div>

            <div class="order-status-action">
                <div class="status-wrapper">
                    <span class="' . $classStato . '"></span>
                    <span>' . $statoFormattato . '</span>
                </div>
                <a href="dettaglioOrdine.php?id=' . $ordine['id_ordine'] . '" class="bottone-primario">Dettagli</a>
            </div>
        </div>';
    }
} else {
    $ordiniHTML = '
        <p>Non hai ancora effettuato ordini.</p>
        <div class="admin-actions">
            <a href="catalogo.php" class="bottone-primario">Inizia lo <span lang="en">shopping</span></a>
        </div>';
}

$templatePath = 'html/paginaUtente.html';
if (file_exists($templatePath)) {
    $template = file_get_contents($templatePath);
    
    // Sostituzioni
    $template = str_replace('[navbar]', $navbarBlock, $template);
    $template = str_replace('[NOME_COMPLETO]', $nomeCompleto, $template);
    $template = str_replace('[EMAIL_UTENTE]', $emailUtente, $template);
    $template = str_replace('[DATA_REGISTRAZIONE]', $dataRegistrazione, $template);
    $template = str_replace('[INDIRIZZO_COMPLETO]', $indirizzoCompleto, $template);
    $template = str_replace('[ORDINI_CONTENT]', $ordiniHTML, $template);
    
    echo $template;
} else {
    die("Errore: Template paginaUtente.html non trovato in html/.");
}
?>