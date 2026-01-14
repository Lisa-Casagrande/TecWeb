<?php
session_start();
require_once 'connessione.php';

/* CONTROLLO METODO E SESSIONE*/
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['user_id'])) {
    header('Location: ../modificaProfilo.php'); 
    exit;
}

$userId = $_SESSION['user_id'];

/*  FUNZIONI DI VALIDAZIONE */
function cleanDB($val) { return trim($val ?? ''); }
function validaTesto($val) { return preg_match("/^[A-Za-z\sÀ-ÿ'’]{2,50}$/u", $val); }
function validaIndirizzo($val) { return preg_match("/^[A-Za-z0-9\sÀ-ÿ'’.,-]{5,100}$/u", $val); }

/*RECUPERO DATI */
$errors = [];
$old = $_POST;

$nome      = cleanDB($_POST['reg_nome'] ?? '');
$cognome   = cleanDB($_POST['reg_cognome'] ?? '');
$citta     = cleanDB($_POST['reg_citta'] ?? '');
$indirizzo = cleanDB($_POST['reg_indirizzo'] ?? '');
$cap       = cleanDB($_POST['reg_cap'] ?? '');

/* VALIDAZIONE */
if (!validaTesto($nome))           $errors['reg_nome'] = "Errore: Nome non valido.";
if (!validaTesto($cognome))        $errors['reg_cognome'] = "Errore: Cognome non valido.";
if (!validaTesto($citta))          $errors['reg_citta'] = "Errore: Città non valida.";
if (!validaIndirizzo($indirizzo))  $errors['reg_indirizzo'] = "Errore: Indirizzo non valido.";
if (!preg_match('/^\d{5}$/', $cap)) $errors['reg_cap'] = "Errore: Il CAP deve contenere esattamente 5 numeri.";

/* GESTIONE ERRORI → REDIRECT */
if (!empty($errors)) {
    $_SESSION['errors'] = $errors;
    $_SESSION['errors']['generale'] = "Attenzione: correggi i campi evidenziati.";
    $_SESSION['old'] = $old;
    header('Location: ../modificaProfilo.php'); 
    exit;
}

/*  AGGIORNAMENTO DB */
try {
    $stmt = $pdo->prepare("
        UPDATE utente 
        SET nome = :nome, 
            cognome = :cognome, 
            indirizzo = :indirizzo, 
            cap = :cap, 
            citta = :citta
        WHERE id_utente = :id
    ");

    $stmt->execute([
        'nome'      => $nome,
        'cognome'   => $cognome,
        'indirizzo' => $indirizzo,
        'cap'       => $cap,
        'citta'     => $citta,
        'id'        => $userId
    ]);

    
    $_SESSION['user_nome'] = $nome;

    $_SESSION['successo'] = "Profilo aggiornato con successo!";

    // Redirect alla pagina utente
    header('Location: ../paginaUtente.php');
    exit;

} catch (PDOException $e) {
    $_SESSION['errors']['generale'] = "Errore durante il salvataggio: " . $e->getMessage();
    header('Location: ../modificaProfilo.php');
    exit;
}