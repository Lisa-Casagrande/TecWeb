<?php
// dettaglioOrdine.php
require_once 'php/connessione.php';
require_once 'php/verificaSessione.php';

// CORREZIONE NAVBAR:
// Includendo navbar.php, la variabile $navbarBlock viene creata automaticamente.
// Non serve usare ob_start() perché navbar.php non fa 'echo'.
require_once 'php/navbar.php'; 

// Controllo ID Ordine
if (!isset($_GET['id'])) {
    header("Location: paginaUtente.php");
    exit;
}

$id_ordine = intval($_GET['id']);
$userId = $_SESSION['user_id'] ?? null;

if (!$userId) {
    header("Location: login.php");
    exit;
}

try {
    // 1. Recupero Dati Ordine
    $stmt_ordine = $pdo->prepare("
        SELECT o.*, u.nome, u.cognome, u.email
        FROM ordine o
        INNER JOIN utente u ON o.id_utente = u.id_utente
        WHERE o.id_ordine = :id_ordine AND o.id_utente = :id_utente
        LIMIT 1
    ");
    $stmt_ordine->execute([
        ':id_ordine' => $id_ordine,
        ':id_utente' => $userId
    ]);
    $ordine = $stmt_ordine->fetch();

    if (!$ordine) {
        die("Ordine non trovato o non accessibile.");
    }

    // 2. Recupero Dettagli Prodotti
    $stmt_dettagli = $pdo->prepare("
        SELECT d.*, 
               p.nome AS nome_prodotto, p.img_path,
               pc.nome_blend, pc.grammi AS grammi_custom
        FROM dettaglio_ordine d
        LEFT JOIN prodotto p ON d.id_prodotto = p.id_prodotto
        LEFT JOIN prodotto_custom pc ON d.id_custom = pc.id_custom
        WHERE d.id_ordine = :id_ordine
    ");
    $stmt_dettagli->execute([':id_ordine' => $id_ordine]);
    $dettagli = $stmt_dettagli->fetchAll();

    // 3. Logica Stato (Classi CSS)
    $mappaStati = [
        'annullato'       => 'stato-rosso',
        'in_attesa'       => 'stato-giallo',
        'in_preparazione' => 'stato-giallo',
        'pagato'          => 'stato-verde',
        'spedito'         => 'stato-verde',
        'consegnato'      => 'stato-blu'
    ];
    $statoKey = $ordine['stato_ord'];
    $classStato = isset($mappaStati[$statoKey]) ? $mappaStati[$statoKey] : 'stato-giallo';
    $statoFormattato = ucfirst(str_replace('_', ' ', $statoKey));

    // 4. Costruzione HTML Lista Articoli
    $listaArticoliHtml = '';

    if (!empty($dettagli)) {
        foreach ($dettagli as $item) {
            $nomeItem = !empty($item['nome_prodotto']) ? htmlspecialchars($item['nome_prodotto']) : htmlspecialchars($item['nome_blend']);
            $tipoItem = !empty($item['nome_prodotto']) ? "Catalogo" : "Blend Custom";
            $quantita = $item['quantita'];
            $prezzoRiga = number_format($item['totale_riga'], 2);

            $listaArticoliHtml .= <<<HTML
            <li class="product-item">
                <div class="product-info">
                    <strong>{$nomeItem}</strong>
                    <span class="product-type">{$tipoItem}</span>
                </div>
                
                <div class="product-pricing">
                    <span class="qty-badge">x{$quantita}</span>
                    <span class="price">€ {$prezzoRiga}</span>
                </div>
            </li>
HTML;
        }

        // Gestione Omaggio
        if ($ordine['omaggio'] == 1) {
            $descOmaggio = htmlspecialchars($ordine['descrizione_omaggio']);
            $listaArticoliHtml .= <<<HTML
            <li class="product-item omaggio-admin-row">
                <div class="product-info">
                    <strong>🎁 Omaggio: {$descOmaggio}</strong>
                    <span class="product-type">Omaggio per ordini sopra i 50 euro</span>
                </div>
                
                <div class="product-pricing">
                    <span class="qty-badge">x1</span>
                    <span class="price">Gratis</span>
                </div>
            </li>
HTML;
        }
    } else {
        $listaArticoliHtml = '<li class="product-item"><p>Nessun articolo trovato.</p></li>';
    }

    // 5. Gestione Note Ordine
    $noteHtml = '';
    if (!empty($ordine['note'])) {
        $noteTesto = htmlspecialchars($ordine['note']);
        $noteHtml = <<<HTML
        <div class="order-notes">
            <strong>Note:</strong>
            <p>{$noteTesto}</p>
        </div>
HTML;
    }

} catch (PDOException $e) {
    die("Errore caricamento dati: " . $e->getMessage());
}

// 6. Caricamento e Rendering Template
$templatePath = 'html/dettaglioOrdine.html';

if (file_exists($templatePath)) {
    $template = file_get_contents($templatePath);

    // Formattazione Date e Valuta
    $dataOrdine = date("d/m/Y H:i", strtotime($ordine['data_ordine']));
    $indirizzo = htmlspecialchars($ordine['indirizzo_spedizione']);
    $sottototale = number_format($ordine['sottototale'], 2);
    $spedizione = number_format($ordine['spese_spedizione'], 2);
    $totale = number_format($ordine['totale'], 2);

    // SOSTITUZIONI
    // Qui usiamo $navbarBlock che è stato generato dall'include 'php/navbar.php'
    $template = str_replace('[navbar]', $navbarBlock, $template);
    
    $template = str_replace('[ID_ORDINE]', $id_ordine, $template);
    $template = str_replace('[CLASSE_STATO]', $classStato, $template);
    $template = str_replace('[STATO_FORMATTATO]', $statoFormattato, $template);
    
    $template = str_replace('[DATA_ORDINE]', $dataOrdine, $template);
    $template = str_replace('[INDIRIZZO_SPEDIZIONE]', $indirizzo, $template);
    
    $template = str_replace('[LISTA_ARTICOLI]', $listaArticoliHtml, $template);
    
    $template = str_replace('[SOTTOTOTALE]', $sottototale, $template);
    $template = str_replace('[SPESE_SPEDIZIONE]', $spedizione, $template);
    $template = str_replace('[TOTALE]', $totale, $template);
    
    $template = str_replace('[NOTE_ORDINE]', $noteHtml, $template);

    echo $template;

} else {
    die("Errore: Template html/dettaglioOrdine.html non trovato.");
}
?>