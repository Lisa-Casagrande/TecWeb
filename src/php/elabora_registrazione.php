<?php
session_start();

// 1. Controllo Metodo
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../registrazione.php');
    exit;
}

// --- FUNZIONI DI VALIDAZIONE ---
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
    return preg_match(
        '/^[a-zA-Z0-9._%+-]{2,}@[a-zA-Z0-9.-]{2,}\.[a-zA-Z]{2,}$/',
        $email
    );
}

// --- RECUPERO DATI ---
$errors = [];
$old = $_POST;

$nome      = clean($_POST['nome'] ?? '');
$cognome   = clean($_POST['cognome'] ?? '');
$dataNasc  = clean($_POST['data-nascita'] ?? '');
$citta     = clean($_POST['citta'] ?? '');
$indirizzo = clean($_POST['indirizzo'] ?? '');
$email     = clean($_POST['email'] ?? '');
$password  = $_POST['password'] ?? '';
$confPass  = $_POST['conferma-password'] ?? '';

// --- LOGICA DI VALIDAZIONE ---
if (!validaTesto($nome))      $errors['nome'] = "Errore: Nome non valido.";
if (!validaTesto($cognome))   $errors['cognome'] = "Errore: Cognome non valido.";
if (!validaMaggiorenne($dataNasc)) $errors['data-nascita'] = "Errore: Devi essere maggiorenne.";
if (!validaTesto($citta))     $errors['citta'] = "Errore: Città non valida.";
if (!validaIndirizzo($indirizzo)) $errors['indirizzo'] = "Errore: Indirizzo non valido.";
if (!validaEmailAvanzata($email)) $errors['email'] = "Errore: Email non valida.";
if (!preg_match("/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&]).{8,}$/", $password)) {
    $errors['password'] = "Errore: Password non sicura.";
}
if ($password !== $confPass) {
    $errors['conf'] = "Errore: Le password non coincidono.";
}

// --- GESTIONE ERRORI ---
if (!empty($errors)) {
    $_SESSION['errors'] = $errors;
    $_SESSION['errors']['generale'] = "Attenzione: correggi gli errori evidenziati.";
    $_SESSION['old'] = $_POST;
    header('Location: ../registrazione.php');
    exit;
}

// --- HASH PASSWORD ---
$hashPassword = password_hash($password, PASSWORD_DEFAULT);

// --- CONNESSIONE DATABASE ---
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
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );
} catch (PDOException $e) {
    die("Errore connessione DB: " . $e->getMessage());
}

// --- CONTROLLO EMAIL GIA' ESISTENTE ---
try {
    $checkEmail = $db->prepare("SELECT id_utente FROM utente WHERE email = :email LIMIT 1");
    $checkEmail->execute([':email' => $email]);

    if ($checkEmail->fetch()) {
        $_SESSION['errors'] = [
            'email' => "Errore: Email già esistente, scegline un'altra.",
            'generale' => "Attenzione: correggi gli errori evidenziati."
        ];
        $_SESSION['old'] = $_POST;
        header('Location: ../registrazione.php');
        exit;
    }
} catch (PDOException $e) {
    $_SESSION['errors'] = ['generale' => "Errore durante il controllo email."];
    $_SESSION['old'] = $_POST;
    header('Location: ../registrazione.php');
    exit;
}

// --- INSERIMENTO NEL DATABASE (ordine corretto) ---
try {
    $stmt = $db->prepare("INSERT INTO utente (email, password_hash, nome, cognome, data_nascita, indirizzo, citta) 
                          VALUES (:email, :password_hash, :nome, :cognome, :data_nascita, :indirizzo, :citta)");
    $stmt->execute([
        ':email'         => $email,
        ':password_hash' => $hashPassword,
        ':nome'          => $nome,
        ':cognome'       => $cognome,
        ':data_nascita'  => $dataNasc,
        ':indirizzo'     => $indirizzo,
        ':citta'         => $citta
    ]);
} catch (PDOException $e) {
    $_SESSION['errors'] = ['generale' => "Errore durante la registrazione, riprova più tardi."];
    $_SESSION['old'] = $_POST;
    header('Location: ../registrazione.php');
    exit;
}

// --- SUCCESSO ---
echo "<h1>Registrazione completata con successo!</h1>";
echo "<p>La tua registrazione è stata effettuata correttamente. Ora puoi accedere al tuo account.</p>";

?>
