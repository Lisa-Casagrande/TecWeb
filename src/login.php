<?php
require_once 'php/verificaSessione.php';

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
        $utente = null;
        $tipoUtente = null;

        // --- Controlla prima nella tabella amministratore ---
        $stmt = $db->prepare("SELECT id_admin as id, nome, cognome, email, password_hash FROM admin WHERE email = :email LIMIT 1");
        $stmt->execute([':email' => $valoreEmail]);
        $admin = $stmt->fetch();

        if ($admin) {
            $utente = $admin;
            $tipoUtente = 'admin';
        } else {
            // --- Controlla nella tabella utente ---
            $stmt = $db->prepare("SELECT id_utente as id, nome, cognome, email, password_hash FROM utente WHERE email = :email LIMIT 1");
            $stmt->execute([':email' => $valoreEmail]);
            $user = $stmt->fetch();

            if ($user) {
                $utente = $user;
                $tipoUtente = 'utente';
            }
        }

        if (!$utente) {
            $erroreEmail = "Errore: Utente non trovato.";
            $classeEmail = "input-error";
        } else {
            // Verifica password
            if (!password_verify($password, $utente['password_hash'])) {
                $errorePassword = "Errore: Password non corretta.";
                $classePassword = "input-error";
            } else {
                // --- LOGIN RIUSCITO: CREA SESSIONE ---
                $_SESSION['user_id'] = $utente['id'];        // ID utente o admin
                $_SESSION['user_email'] = $utente['email'];
                $_SESSION['user_nome'] = $utente['nome'];
                $_SESSION['user_tipo'] = $tipoUtente;       // 'admin' o 'utente'
                $_SESSION['logged_in'] = true;              // flag universale di login

                // --- Reindirizza in base al tipo ---
                if ($tipoUtente === 'admin') {
                    header("Location: dashboardAdmin.php");
                } else {
                    header("Location: paginaUtente.php");
                }
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
