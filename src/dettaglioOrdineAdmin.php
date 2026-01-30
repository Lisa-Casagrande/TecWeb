<?php
require_once 'php/connessione.php';
require_once 'php/verificaSessioneAdmin.php';

if (!isset($_GET['id'])) {
    header("Location: gestioneOrdini.php");
    exit;
}
$id_ordine = intval($_GET['id']);

$errore = null;
$msgSuccesso = '';

// GESTIONE CAMBIO STATO
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nuovo_stato'])) {
    $nuovo_stato = $_POST['nuovo_stato'];
    try {
        $stmtUpdate = $pdo->prepare("UPDATE ordine SET stato_ord = ? WHERE id_ordine = ?");
        $stmtUpdate->execute([$nuovo_stato, $id_ordine]);
        //ricarica la pagina con messaggio
        header("Location: dettaglioOrdineAdmin.php?id=$id_ordine&msg=updated");
        exit;
    } catch (PDOException $e) {
        $errore = "Errore durante l'aggiornamento: " . $e->getMessage();
    }
}

if (isset($_GET['msg']) && $_GET['msg'] == 'updated') {
    $msgSuccesso = "Stato ordine aggiornato con successo!";
}

// RECUPERO DATI
try {
    //ordine e utente
    $sqlOrdine = "SELECT o.*, u.nome, u.cognome, u.email 
                  FROM ordine o 
                  JOIN utente u ON o.id_utente = u.id_utente 
                  WHERE o.id_ordine = ?";
    $stmt = $pdo->prepare($sqlOrdine);
    $stmt->execute([$id_ordine]);
    $ordine = $stmt->fetch();

    if (!$ordine) {
        die("Ordine non trovato.");
    }
    //dettagli ordine
    $sqlDettagli = "SELECT do.*, 
                           p.nome AS nome_prodotto, p.img_path,
                           pc.nome_blend, pc.grammi AS grammi_custom
                    FROM dettaglio_ordine do 
                    LEFT JOIN prodotto p ON do.id_prodotto = p.id_prodotto 
                    LEFT JOIN prodotto_custom pc ON do.id_custom = pc.id_custom
                    WHERE do.id_ordine = ?";
    $stmtDet = $pdo->prepare($sqlDettagli);
    $stmtDet->execute([$id_ordine]);
    $dettagli = $stmtDet->fetchAll();

} catch (PDOException $e) {
    die("Errore caricamento dati: " . $e->getMessage());
}

// GENERAZIONE MENU ADMIN
$menuVociAdmin = [
    'dashboardAdmin.php' => '<span lang="en">Dashboard</span>',
    'gestioneProdotti.php' => 'Prodotti',
    'gestioneIngredienti.php' => 'Ingredienti',
    'gestioneOrdini.php' => 'Ordini'
];

$currentPage = basename($_SERVER['PHP_SELF']);
$menuHtml = '';
$leafIcon = '<svg class="nav-leaf-icon" xmlns="http://www.w3.org/2000/svg" viewBox="-77 79 100 100" aria-hidden="true">
                <path d="M-2.5,151.2C13,135.8,16.8,83.6,16.8,83.6s-10.7,6.8-27.5,8.2c-15.8,1.4-30.5,3.6-39.1,12.2c-13.3,13.3-16.6,32.1-9,45.5c10.5-17.8,45-33.5,45-33.5C-38.3,133.4-54.4,150-71.2,174l9.3,1.1c0,0,6.1-11.1,11.5-16.4C-37,168.1-16.6,165.3-2.5,151.2z"/>
            </svg>';

foreach ($menuVociAdmin as $pagina => $testo) {
    $isActive = ($currentPage === $pagina) || ($pagina === 'gestioneOrdini.php'); 
    $classLi = $isActive ? ' class="current-page"' : '';
    
    if ($isActive) {
        $menuHtml .= "<li$classLi><span class=\"nav-link\">$leafIcon$testo</span></li>\n";
    } else {
        $menuHtml .= "<li$classLi><a href=\"$pagina\" class=\"nav-link\">$testo</a></li>\n";
    }
}

// GENERAZIONE CONTENUTO
//Badge Stato
$stato = $ordine['stato_ord'];
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

//Opzioni Select Stato (con 'selected' automatico)
$opzioniStatoArr = [
    'in_attesa' => 'In attesa',
    'pagato' => 'Pagato',
    'in_preparazione' => 'In preparazione',
    'spedito' => 'Spedito',
    'consegnato' => 'Consegnato',
    'annullato' => 'Annullato'
];
$optionsHtml = '';
foreach ($opzioniStatoArr as $val => $label) {
    $sel = ($stato === $val) ? 'selected' : '';
    $optionsHtml .= "<option value=\"$val\" $sel>$label</option>";
}

//Lista Prodotti HTML
$listaProdottiHtml = '';
foreach ($dettagli as $item) {
    $nomeItem = !empty($item['nome_prodotto']) ? htmlspecialchars($item['nome_prodotto']) : htmlspecialchars($item['nome_blend']);
    $tipoItem = !empty($item['nome_prodotto']) ? "Catalogo" : "Blend Custom";
    $prezzoFmt = number_format($item['totale_riga'], 2);
    
    $listaProdottiHtml .= <<<HTML
    <li class="product-item">
        <div class="product-info">
            <strong>{$nomeItem}</strong>
            <span class="product-type">{$tipoItem}</span>
        </div>
        
        <div class="product-pricing">
            <span class="qty-badge">x{$item['quantita']}</span>
            <span class="price">€ {$prezzoFmt}</span>
        </div>
    </li>
HTML;
}

//Omaggio
if ($ordine['omaggio'] == 1) {
    $descOmaggio = htmlspecialchars($ordine['descrizione_omaggio']);
    $listaProdottiHtml .= <<<HTML
    <li class="product-item">
        <div class="product-info">
            <strong>Omaggio: {$descOmaggio}</strong>
            <span class="product-type">Omaggio per ordini sopra i 50 euro</span>
        </div>
        <div class="product-pricing">
            <span class="qty-badge">x1</span>
            <span class="price">Gratis</span>
        </div>
    </li>
HTML;
}

//Note
$noteBlock = '';
if (!empty($ordine['note'])) {
    $noteText = htmlspecialchars($ordine['note']);
    $noteBlock = <<<HTML
    <div class="order-notes">
        <strong>Note del cliente:</strong>
        <p>{$noteText}</p>
    </div>
HTML;
}

//Messaggi
$msgHtml = '';
if ($msgSuccesso) {
    $msgHtml = "<p class=\"messaggio-successo\">$msgSuccesso</p>";
}
if ($errore) {
    $msgHtml .= "<p class=\"errorSuggestion\">" . htmlspecialchars($errore) . "</p>";
}

//CARICAMENTO TEMPLATE
$templatePath = 'html/dettaglioOrdineAdmin.html';

if (file_exists($templatePath)) {
    $html = file_get_contents($templatePath);
    $html = str_replace('[ACTION_URL]', "dettaglioOrdineAdmin.php?id=$id_ordine", $html);
    $html = str_replace('[menuVoci]', $menuHtml, $html);
    $html = str_replace('[ID_ORDINE]', $id_ordine, $html);
    $html = str_replace('[CLASS_STATO]', $classStato, $html);
    $html = str_replace('[STATO_TEXT]', $statoFormattato, $html);
    $html = str_replace('[MESSAGGI_SISTEMA]', $msgHtml, $html);
    
    $nomeCompleto = htmlspecialchars($ordine['nome'] . " " . $ordine['cognome']);
    $email = htmlspecialchars($ordine['email']);
    $indirizzo = htmlspecialchars($ordine['indirizzo_spedizione']);
    $dataOrdine = date("d/m/Y H:i", strtotime($ordine['data_ordine']));
    
    $html = str_replace('[NOME_CLIENTE]', $nomeCompleto, $html);
    $html = str_replace('[EMAIL_CLIENTE]', $email, $html);
    $html = str_replace('[INDIRIZZO_SPEDIZIONE]', $indirizzo, $html);
    $html = str_replace('[DATA_ORDINE]', $dataOrdine, $html);
    
    $html = str_replace('[OPTIONS_STATO]', $optionsHtml, $html);
    
    $html = str_replace('[LISTA_PRODOTTI]', $listaProdottiHtml, $html);
    $html = str_replace('[SOTTOTOTALE]', number_format($ordine['sottototale'], 2), $html);
    $html = str_replace('[SPEDIZIONE]', number_format($ordine['spese_spedizione'], 2), $html);
    $html = str_replace('[TOTALE]', number_format($ordine['totale'], 2), $html);
    $html = str_replace('[NOTE_CLIENTE]', $noteBlock, $html);
    
    echo $html;
} else {
    die("Errore: Template dettaglioOrdineAdmin.html non trovato.");
}
?>