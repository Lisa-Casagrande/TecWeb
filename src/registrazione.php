<?php
// Gestione sessioni
if (session_status() === PHP_SESSION_NONE) { 
    session_start(); 
}

// Se qualcuno è già loggato → logout automatico
if (isset($_SESSION['id_utente'])) {
    session_unset();
    session_destroy();
}

$successoRegistrazione = isset($_GET['success']) ? "Registrazione avvenuta con successo! Ora puoi accedere." : "";

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
    // Errore generale
    '[erroreGenerale]' => isset($errors['generale']) ? htmlspecialchars($errors['generale'], ENT_QUOTES | ENT_HTML5, 'UTF-8') : '',

    // Errori specifici per ogni campo
    '[erroreNome]'       => isset($errors['reg_nome']) ? htmlspecialchars($errors['reg_nome'], ENT_QUOTES | ENT_HTML5, 'UTF-8') : '',
    '[erroreCognome]'    => isset($errors['reg_cognome']) ? htmlspecialchars($errors['reg_cognome'], ENT_QUOTES | ENT_HTML5, 'UTF-8') : '',
    '[erroreNascita]'    => isset($errors['reg_data-nascita']) ? htmlspecialchars($errors['reg_data-nascita'], ENT_QUOTES | ENT_HTML5, 'UTF-8') : '',
    '[erroreCitta]'      => isset($errors['reg_citta']) ? htmlspecialchars($errors['reg_citta'], ENT_QUOTES | ENT_HTML5, 'UTF-8') : '',
    '[erroreIndirizzo]'  => isset($errors['reg_indirizzo']) ? htmlspecialchars($errors['reg_indirizzo'], ENT_QUOTES | ENT_HTML5, 'UTF-8') : '',
    '[erroreCap]'        => isset($errors['reg_cap']) ? htmlspecialchars($errors['reg_cap'], ENT_QUOTES | ENT_HTML5, 'UTF-8') : '',
    '[erroreEmail]'      => isset($errors['reg_email']) ? htmlspecialchars($errors['reg_email'], ENT_QUOTES | ENT_HTML5, 'UTF-8') : '',
    '[errorePassword]'   => isset($errors['reg_password']) ? htmlspecialchars($errors['reg_password'], ENT_QUOTES | ENT_HTML5, 'UTF-8') : '',
    '[erroreConferma]'   => isset($errors['reg_conf']) ? htmlspecialchars($errors['reg_conf'], ENT_QUOTES | ENT_HTML5, 'UTF-8') : '',

    // Valori precedenti (per ripopolare i campi, **non password**)
    '[valoreNome]'       => htmlspecialchars($old['reg_nome'] ?? '', ENT_QUOTES | ENT_HTML5, 'UTF-8'),
    '[valoreCognome]'    => htmlspecialchars($old['reg_cognome'] ?? '', ENT_QUOTES | ENT_HTML5, 'UTF-8'),
    '[valoreData]'       => htmlspecialchars($old['reg_data-nascita'] ?? '', ENT_QUOTES | ENT_HTML5, 'UTF-8'),
    '[valoreCitta]'      => htmlspecialchars($old['reg_citta'] ?? '', ENT_QUOTES | ENT_HTML5, 'UTF-8'),
    '[valoreIndirizzo]'  => htmlspecialchars($old['reg_indirizzo'] ?? '', ENT_QUOTES | ENT_HTML5, 'UTF-8'),
    '[valoreCap]'        => htmlspecialchars($old['reg_cap'] ?? '', ENT_QUOTES | ENT_HTML5, 'UTF-8'),
    '[valoreEmail]'      => htmlspecialchars($old['reg_email'] ?? '', ENT_QUOTES | ENT_HTML5, 'UTF-8'),
    '[valorePassword]'   => '', 

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


require_once 'php/navbar.php';

//caricamento del template HTML
$templatePath = __DIR__ . '/html/Area_registrazione.html';

if (file_exists($templatePath)) {
    $template = file_get_contents($templatePath);
    //sostituzione della navbar
    $template = str_replace('[navbar]', $navbarBlock, $template);
    //sostituzione di tutti i placeholder definiti nell'array
    $template = str_replace(array_keys($sostituzioni), array_values($sostituzioni), $template);

    echo $template;

    } else {
        die("Errore: Template Area_registrazione.html non trovato.");
    }
?>
