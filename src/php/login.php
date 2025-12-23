<?php
session_start();

// --- CONNESSIONE DB ---
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
    die("Errore DB");
}

// --- DATI FORM ---
$email = trim($_POST["email"] ?? '');
$password = trim($_POST["password"] ?? '');

if (!$email || !$password) {
    die("Dati mancanti");
}

// --- CONTROLLO ADMIN ---
$stmt = $db->prepare("SELECT * FROM admin WHERE email = :email LIMIT 1");
$stmt->execute([':email' => $email]);
$admin = $stmt->fetch();

if ($admin && password_verify($password, $admin['password_hash'])) {
    $_SESSION['ruolo'] = 'admin';
    $_SESSION['nome'] = $admin['nome'];
    $_SESSION['cognome'] = $admin['cognome'];
    $_SESSION['email'] = $admin['email'];

    header("Location: ../admin/dashboard.php");
    exit;
}

// --- CONTROLLO UTENTE ---
$stmt = $db->prepare("SELECT * FROM utente WHERE email = :email LIMIT 1");
$stmt->execute([':email' => $email]);
$utente = $stmt->fetch();

if ($utente && password_verify($password, $utente['password_hash'])) {
    $_SESSION['ruolo'] = 'utente';
    $_SESSION['nome'] = $utente['nome'];
    $_SESSION['cognome'] = $utente['cognome'];
    $_SESSION['email'] = $utente['email'];

    header("Location: ../account.php");
    exit;
}

// --- FALLIMENTO ---
die("Credenziali errate. <a href='../login.html'>Riprova</a>");