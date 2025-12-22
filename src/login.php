<?php
session_start();

// --- Connessione al database ---
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

// --- Inizializza variabili per placeholder ---
$valoreEmail = '';
$classeEmail = '';
$classePassword = '';
$erroreEmail = '';
$errorePassword = '';
$erroreGenerale = '';

// --- Controlla se il form è stato inviato ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $valoreEmail = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    // Validazione base
    if (empty($valoreEmail)) {
        $erroreEmail = "Inserisci l'email.";
        $classeEmail = "input-error";
    }
    if (empty($password)) {
        $errorePassword = "Inserisci la password.";
        $classePassword = "input-error";
    }

    if (!$erroreEmail && !$errorePassword) {
        // Controlla utente nel DB
        $stmt = $db->prepare("SELECT email, password_hash FROM utente WHERE email = :email LIMIT 1");
        $stmt->execute([':email' => $valoreEmail]);
        $utente = $stmt->fetch();

        if (!$utente) {
            $erroreEmail = "Errore: Utente non trovato.";
            $classeEmail = "input-error";
        } else {
            // Verifica password
            if (!password_verify($password, $utente['password_hash'])) {
                $errorePassword = "Errore: Password non corretta.";
                $classePassword = "input-error";
            } else {
                // Login riuscito, memorizza email in sessione
                $_SESSION['user_email'] = $utente['email'];
                header("Location: dashboard.php");
                exit;
            }
        }
    }
}

// --- Carica HTML e sostituisci placeholder ---
$html = file_get_contents('login_utente.html'); // Assicurati che il file esista
$html = str_replace(
    ['[valoreEmail]', '[classeEmail]', '[classePassword]', '[erroreEmail]', '[errorePassword]', '[erroreGenerale]'],
    [$valoreEmail, $classeEmail, $classePassword, $erroreEmail, $errorePassword, $erroreGenerale],
    $html
);

echo $html;
?>