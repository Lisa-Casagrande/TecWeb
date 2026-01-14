<?php
require_once 'php/connessione.php';
require_once 'php/verificaSessioneAdmin.php';

// Controllo ID
if (!isset($_GET['id'])) {
    header("Location: gestioneOrdini.php");
    exit;
}
$id_ordine = intval($_GET['id']);

// Variabili per il template
$ordine = null;
$dettagli = [];
$classStato = '';
$statoFormattato = '';
$stato = '';
$errore = null;

// 1. GESTIONE CAMBIO STATO
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nuovo_stato'])) {
    $nuovo_stato = $_POST['nuovo_stato'];
    try {
        $stmtUpdate = $pdo->prepare("UPDATE ordine SET stato_ord = ? WHERE id_ordine = ?");
        $stmtUpdate->execute([$nuovo_stato, $id_ordine]);
        header("Location: dettaglioOrdineAdmin.php?id=$id_ordine&msg=updated");
        exit;
    } catch (PDOException $e) {
        $errore = "Errore: " . $e->getMessage();
    }
}

// 2. RECUPERO DATI ORDINE + UTENTE
try {
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

    // 3. RECUPERO DETTAGLI (Prodotti Standard + Custom Blend)
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

    // 4. Logica colori stato
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

} catch (PDOException $e) {
    die("Errore caricamento dati: " . $e->getMessage());
}

$templatePath = __DIR__ . '/html/dettaglioOrdineAdmin.html';
if (file_exists($templatePath)) {
    include $templatePath;
} else {
    die("Errore: template non trovato");
}
?>