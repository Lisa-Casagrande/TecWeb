<?php
session_start();
require_once 'connessione.php';

/* =============================
   CONTROLLO METODO
============================= */
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /registrazione.php');
    exit;
}

/* =============================
   FUNZIONI
============================= */
function cleanDB($val) {
    return trim($val ?? '');
}

function validaTesto($val) {
    return preg_match("/^[A-Za-z\sÀ-ÿ'’]{2,50}$/u", $val);
}

function validaIndirizzo($val) {
    return preg_match("/^[A-Za-z0-9\sÀ-ÿ'’.,-]{5,100}$/u", $val);
}

function validaMaggiorenne($data) {
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $data)) return false;
    $nascita = new DateTime($data);
    $oggi = new DateTime();
    return $oggi->diff($nascita)->y >= 18;
}

function validaEmailAvanzata($email) {
    if (!preg_match('/^[A-Za-z0-9][A-Za-z0-9._%+-]{1,62}[A-Za-z0-9]@[A-Za-z0-9][A-Za-z0-9.-]{1,62}[A-Za-z0-9]\.[A-Za-z]{2,}$/', $email)) {
        return false;
    }
    if (preg_match('/\s/', $email)) return false;
    if (preg_match('/(\.\.|--|__)/', $email)) return false;

    [$locale, $dominio] = explode('@', $email);
    if (preg_match('/^[+.\-]|[+.\-]$/', $locale)) return false;
    if (substr($dominio, -1) === '.') return false;

    return true;
}

/* =============================
   RECUPERO DATI
============================= */
$errors = [];
$old = $_POST;

// Pulizia valori per DB (senza htmlspecialchars)
$nome       = cleanDB($_POST['reg_nome'] ?? '');
$cognome    = cleanDB($_POST['reg_cognome'] ?? '');
$dataNasc   = cleanDB($_POST['reg_data-nascita'] ?? '');
$citta      = cleanDB($_POST['reg_citta'] ?? '');
$indirizzo  = cleanDB($_POST['reg_indirizzo'] ?? '');
$email      = cleanDB($_POST['reg_email'] ?? '');
$cap        = cleanDB($_POST['reg_cap'] ?? '');
$password   = $_POST['reg_password'] ?? '';
$confPass   = $_POST['reg_conferma-password'] ?? '';

/* =============================
   VALIDAZIONE
============================= */
if (!validaTesto($nome))           $errors['reg_nome'] = "Errore: Nome non valido.";
if (!validaTesto($cognome))        $errors['reg_cognome'] = "Errore: Cognome non valido.";
if (!validaMaggiorenne($dataNasc)) $errors['reg_data-nascita'] = "Errore: Devi essere maggiorenne.";
if (!validaTesto($citta))          $errors['reg_citta'] = "Errore: Città non valida.";
if (!validaIndirizzo($indirizzo))  $errors['reg_indirizzo'] = "Errore: Indirizzo non valido.";
if (!preg_match('/^\d{5}$/', $cap)) $errors['reg_cap'] = "Errore: Il CAP deve contenere esattamente 5 numeri.";
if (!validaEmailAvanzata($email))  $errors['reg_email'] = "Errore: Email non valida.";
if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&]).{8,}$/', $password)) $errors['reg_password'] = "Errore: Password non sicura.";
if ($password !== $confPass)       $errors['reg_conf'] = "Errore: Le password non coincidono.";

/* =============================
   ERRORI → RITORNO AL FORM
============================= */
if (!empty($errors)) {
    unset($old['reg_password'], $old['reg_conferma-password']); // non salvare password in sessione
    $_SESSION['errors'] = $errors;
    $_SESSION['errors']['generale'] = "Attenzione: correggi gli errori evidenziati.";
    $_SESSION['old'] = $old;
    header('Location: ../registrazione.php');
    exit;
}


/* =============================
   HASH PASSWORD
============================= */
$hashPassword = password_hash($password, PASSWORD_DEFAULT);

/* =============================
// EMAIL GIÀ ESISTENTE
============================= */
$stmt = $pdo->prepare("SELECT id_utente FROM utente WHERE email = :email LIMIT 1");
$stmt->execute(['email' => $email]);
if ($stmt->fetch()) {
    $_SESSION['errors'] = [
        'reg_email' => "Errore: Email già registrata.",
        'generale'  => "Attenzione: correggi gli errori evidenziati."
    ];
    $_SESSION['old'] = $old;
    header('Location: ../registrazione.php');
    exit;
}

/* =============================
// INSERIMENTO UTENTE
============================= */
$stmt = $pdo->prepare("
    INSERT INTO utente 
    (email, password_hash, nome, cognome, data_nascita, indirizzo, cap, citta)
    VALUES
    (:reg_email, :reg_password, :reg_nome, :reg_cognome, :reg_data_nascita, :reg_indirizzo, :reg_cap, :reg_citta)
");

$stmt->execute([
    'reg_email'       => $email,
    'reg_password'    => $hashPassword,
    'reg_nome'        => $nome,
    'reg_cognome'     => $cognome,
    'reg_data_nascita'=> $dataNasc,
    'reg_indirizzo'   => $indirizzo,
    'reg_cap'         => $cap,
    'reg_citta'       => $citta
]);

/* =============================
   SUCCESSO → REDIRECT LOGIN
============================= */
unset($_SESSION['old'], $_SESSION['errors']);
header('Location: ../login.php');
exit;
