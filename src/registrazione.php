<?php
if (session_status() === PHP_SESSION_NONE) { 
    session_start(); 
 
/* Se qualcuno è già loggato → logout automatico */
if (isset($_SESSION['id_utente'])) {
    session_unset();
    session_destroy();
}
}

/// Carica il template HTML
$template = file_get_contents('registra_utente.html');

//  Genera Navbar
ob_start();
require 'navbar.php';
$navbar = ob_get_clean();

// Recupera eventuali errori e valori precedenti
$errors = $_SESSION['errors'] ?? [];
$old = $_SESSION['old'] ?? [];
unset($_SESSION['errors'], $_SESSION['old']);

// Funzione helper per decidere la classe CSS iniziale
function getClasse($campo, $errors, $old) {
    if (isset($errors[$campo])) return 'input-error';
    // Solo se il campo ha un valore **non vuoto e non è password** diventa valido
    if (isset($old[$campo]) && !empty($old[$campo]) && $campo !== 'reg_password' && $campo !== 'reg_conf') return 'input-valid';
    return '';
}
// Calcola la data massima per essere maggiorenne (18 anni)
$maxDataNascita = date('Y-m-d', strtotime('-18 years'));

// Prepara le sostituzioni per i placeholder nel template
$sostituzioni = [
    // NAVBAR
    '[PLACEHOLDER_NAVBAR]' => $navbar,
    // Errore generale
    '[erroreGenerale]' => $errors['generale'] ?? '',

    // Errori specifici per ogni campo
    '[erroreNome]'       => $errors['reg_nome'] ?? '',
    '[erroreCognome]'    => $errors['reg_cognome'] ?? '',
    '[erroreNascita]'    => $errors['reg_data-nascita'] ?? '',
    '[erroreCitta]'      => $errors['reg_citta'] ?? '',
    '[erroreIndirizzo]'  => $errors['reg_indirizzo'] ?? '',
    '[erroreCap]'        => $errors['reg_cap'] ?? '',
    '[erroreEmail]'      => $errors['reg_email'] ?? '',
    '[errorePassword]'   => $errors['reg_password'] ?? '',
    '[erroreConferma]'   => $errors['reg_conf'] ?? '',

    // Valori precedenti (per ripopolare i campi)
    '[valoreNome]'       => htmlspecialchars($old['reg_nome'] ?? ''),
    '[valoreCognome]'    => htmlspecialchars($old['reg_cognome'] ?? ''),
    '[valoreData]'       => htmlspecialchars($old['reg_data-nascita'] ?? ''),
    '[valoreCitta]'      => htmlspecialchars($old['reg_citta'] ?? ''),
    '[valoreIndirizzo]'  => htmlspecialchars($old['reg_indirizzo'] ?? ''),
    '[valoreCap]'        => htmlspecialchars($old['reg_cap'] ?? ''),
    '[valoreEmail]'      => htmlspecialchars($old['reg_email'] ?? ''),
    '[valorePassword]'  => htmlspecialchars($old['reg_password'] ?? ''),
    
    // Classi CSS per input
    '[classeNome]'       => getClasse('reg_nome', $errors, $old),
    '[classeCognome]'    => getClasse('reg_cognome', $errors, $old),
    '[classeData]'       => getClasse('reg_data-nascita', $errors, $old),
    '[classeCitta]'      => getClasse('reg_citta', $errors, $old),
    '[classeIndirizzo]'  => getClasse('reg_indirizzo', $errors, $old),
    '[classeCap]'        => getClasse('reg_cap', $errors, $old),
    '[classeEmail]'      => getClasse('reg_email', $errors, $old),
    '[classePassword]'   => getClasse('reg_password', $errors, $old),
    '[classeConferma]'   => getClasse('reg_conf', $errors, $old),

    // Data massima per la nascita
    '[maxDataNascita]'   => $maxDataNascita
];

// Applica le sostituzioni al template
$output = str_replace(array_keys($sostituzioni), array_values($sostituzioni), $template);

// Stampa il risultato
echo $output;
?>