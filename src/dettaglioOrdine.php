<?php
require_once 'php/connessione.php';
require_once 'php/verificaSessione.php';

// Controllo se è stato passato l'id ordine
if (!isset($_GET['id'])) {
    header("Location: paginaUtente.php");
    exit;
}

$id_ordine = intval($_GET['id']);

// Variabili per il template
$ordine = null;
$dettagli = [];
$classStato = '';
$statoFormattato = '';

try {
    // Recupera ID utente dalla sessione o rimanda a pagina login
    $userId = $_SESSION['user_id'] ?? null;
    if (!$userId) {
        header("Location: login.php");
        exit;
    }

    // Recupera dati ordine solo se dell'utente
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

    // Recupero dettagli prodotti
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
    $dettagli = $stmt_dettagli->fetchAll(); // array vuoto se non ci sono prodotti

    // Logica colori stato (come admin)
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

$templatePath = __DIR__ . '/html/dettaglioOrdine.html';
if (file_exists($templatePath)) {
    include $templatePath;
} else {
    die("Errore: template non trovato");
}
?>