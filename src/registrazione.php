<?php
if (session_status() === PHP_SESSION_NONE) { 
    session_start(); 
}

// Carica il template HTML
$template = file_get_contents('registra_utente.html');

// Recupera eventuali errori e valori precedenti
$errors = $_SESSION['errors'] ?? [];
$old = $_SESSION['old'] ?? [];
unset($_SESSION['errors'], $_SESSION['old']);

// Funzione helper per decidere la classe CSS iniziale
function getClasse($campo, $errors, $old) {
    if (isset($errors[$campo])) return 'input-error';
    if (isset($old[$campo]) && !empty($old[$campo])) return 'input-valid';
    return '';
}

// Calcola la data massima per essere maggiorenne (18 anni)
$maxDataNascita = date('Y-m-d', strtotime('-18 years'));

// Prepara le sostituzioni per i placeholder nel template
$sostituzioni = [
    // Errore generale
    '[erroreGenerale]' => $errors['generale'] ?? '',

    // Errori specifici per ogni campo
    '[erroreNome]'       => $errors['nome'] ?? '',
    '[erroreCognome]'    => $errors['cognome'] ?? '',
    '[erroreNascita]'    => $errors['data-nascita'] ?? '',
    '[erroreCitta]'      => $errors['citta'] ?? '',
    '[erroreIndirizzo]'  => $errors['indirizzo'] ?? '',
    '[erroreEmail]'      => $errors['email'] ?? '',
    '[errorePassword]'   => $errors['password'] ?? '',
    '[erroreConferma]'   => $errors['conf'] ?? '',

    // Valori precedenti (per ripopolare i campi)
    '[valoreNome]'       => htmlspecialchars($old['nome'] ?? ''),
    '[valoreCognome]'    => htmlspecialchars($old['cognome'] ?? ''),
    '[valoreData]'       => htmlspecialchars($old['data-nascita'] ?? ''),
    '[valoreCitta]'      => htmlspecialchars($old['citta'] ?? ''),
    '[valoreIndirizzo]'  => htmlspecialchars($old['indirizzo'] ?? ''),
    '[valoreEmail]'      => htmlspecialchars($old['email'] ?? ''),

    // Classi CSS per input
    '[classeNome]'       => getClasse('nome', $errors, $old),
    '[classeCognome]'    => getClasse('cognome', $errors, $old),
    '[classeData]'       => getClasse('data-nascita', $errors, $old),
    '[classeCitta]'      => getClasse('citta', $errors, $old),
    '[classeIndirizzo]'  => getClasse('indirizzo', $errors, $old),
    '[classeEmail]'      => getClasse('email', $errors, $old),
    '[classePassword]'   => getClasse('password', $errors, $old),
    '[classeConferma]'   => getClasse('conf', $errors, $old),

    // Data massima per la nascita
    '[maxDataNascita]'   => $maxDataNascita
];

// Applica le sostituzioni al template
$output = str_replace(array_keys($sostituzioni), array_values($sostituzioni), $template);

// Stampa il risultato
echo $output;
?>