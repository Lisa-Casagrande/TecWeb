<?php
require_once 'php/verificaSessione.php';
require_once 'php/connessione.php';

if (session_status() === PHP_SESSION_NONE) session_start();

$userId = $_SESSION['user_id']; // Assunto dalla sessione di login

require_once 'php/navbar.php';

// 1. Recupero dati attuali dal DB (se non ci sono dati "vecchi" da errori di validazione)
$stmt = $pdo->prepare("SELECT nome, cognome, email, data_nascita, indirizzo, citta, cap FROM utente WHERE id_utente = :id");
$stmt->execute([':id' => $userId]);
$utenteDB = $stmt->fetch(PDO::FETCH_ASSOC);

// 2. Preparazione dati per il template (priorità a $_SESSION['old'] se presente)
$old = $_SESSION['old'] ?? [];
$errors = $_SESSION['errors'] ?? [];

$data = [
    'valoreNome'        => htmlspecialchars($old['reg_nome'] ?? $utenteDB['nome']),
    'valoreCognome'     => htmlspecialchars($old['reg_cognome'] ?? $utenteDB['cognome']),
    'valoreIndirizzo'   => htmlspecialchars($old['reg_indirizzo'] ?? $utenteDB['indirizzo']),
    'valoreCitta'       => htmlspecialchars($old['reg_citta'] ?? $utenteDB['citta']),
    'valoreCap'         => htmlspecialchars($old['reg_cap'] ?? $utenteDB['cap']),
    'erroreGenerale'    => $errors['generale'] ?? '',
    'successoGenerale'  => $_SESSION['successo'] ?? ''
];

// Pulizia sessione messaggi dopo la lettura
unset($_SESSION['errors'], $_SESSION['old'], $_SESSION['successo']);

//Gestione Errori specifici per classi CSS e messaggi
$campi = ['reg_nome', 'reg_cognome','reg_indirizzo', 'reg_citta', 'reg_cap'];
foreach ($campi as $campo) {
    $data['err_' . $campo] = $errors[$campo] ?? '';
    $data['css_' . $campo] = isset($errors[$campo]) ? 'input-error' : '';
}

//caricamento template
$templatePath = 'html/Modifica_profilo.html';

if (file_exists($templatePath)) {
    $template = file_get_contents($templatePath);
    
    // Sostituzione Navbar
    $template = str_replace('[navbar]', $navbarBlock, $template);
    
    // Sostituzione placeholder dati
    foreach ($data as $key => $value) {
        // Sostituisce ad esempio [valoreNome] con il valore reale
        $template = str_replace("[$key]", $value, $template);
    }
    
    echo $template;
} else {
    die("Errore: Template Modifica_profilo.html non trovato.");
}
?>