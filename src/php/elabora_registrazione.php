<?php
session_start();

/* =============================
   CONTROLLO METODO
============================= */
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../registrazione.php');
    exit;
}

/* =============================
   FUNZIONI
============================= */
function clean($val) {
    return trim(htmlspecialchars($val ?? '', ENT_QUOTES, 'UTF-8'));
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

$nome       = clean($_POST['reg_nome'] ?? '');
$cognome    = clean($_POST['reg_cognome'] ?? '');
$dataNasc   = clean($_POST['reg_data-nascita'] ?? '');
$citta      = clean($_POST['reg_citta'] ?? '');
$indirizzo  = clean($_POST['reg_indirizzo'] ?? '');
$email      = clean($_POST['reg_email'] ?? '');
$password   = $_POST['reg_password'] ?? '';
$confPass   = $_POST['reg_conferma-password'] ?? '';
$cap        = clean($_POST['reg_cap'] ?? '');

/* =============================
   VALIDAZIONE
============================= */
if (!validaTesto($nome))        $errors['reg_nome'] = "Errore: Nome non valido.";
if (!validaTesto($cognome))     $errors['reg_cognome'] = "Errore: Cognome non valido.";
if (!validaMaggiorenne($dataNasc)) $errors['reg_data-nascita'] = "Errore: Devi essere maggiorenne.";
if (!validaTesto($citta))       $errors['reg_citta'] = "Errore: Città non valida.";
if (!validaIndirizzo($indirizzo)) $errors['reg_indirizzo'] = "Errore: Indirizzo non valido.";
if (!preg_match('/^\d{5}$/', $cap)) $errors['reg_cap'] = "Errore: Il CAP deve contenere esattamente 5 numeri.";
if (!validaEmailAvanzata($email)) $errors['reg_email'] = "Errore: Email non valida.";
if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&]).{8,}$/', $password)) $errors['reg_password'] = "Errore: Password non sicura.";
if ($password !== $confPass)     $errors['reg_conf'] = "Errore: Le password non coincidono.";

/* =============================
   ERRORI → RITORNO AL FORM
============================= */
if (!empty($errors)) {
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
   CONNESSIONE DB
============================= */
$dbHost = getenv('DB_HOST') ?: 'localhost';
$dbName = getenv('DB_NAME') ?: 'db_InfuseMe';
$dbUser = getenv('DB_USER') ?: 'infuseme_user';
$dbPass = getenv('DB_PASSWORD') ?: 'InfuseMe123!';

try {
    $db = new PDO(
        "mysql:host=$dbHost;dbname=$dbName;charset=utf8mb4",
        $dbUser,
        $dbPass,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );
} catch (PDOException $e) {
    die("Errore connessione DB");
}

/* =============================
   EMAIL GIÀ ESISTENTE
============================= */
$stmt = $db->prepare("SELECT id_utente FROM utente WHERE email = :email LIMIT 1");
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
   INSERIMENTO UTENTE
============================= */
$stmt = $db->prepare("
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
