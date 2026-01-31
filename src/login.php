<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'php/connessione.php';

// --- Variabili per il template ---
$data = [
    'valoreEmail' => '',
    'classeEmail' => '',
    'classePassword' => '',
    'erroreEmail' => '',
    'errorePassword' => '',
    'erroreGenerale' => ''
];

if (isset($_GET['success'])) {
    $data['erroreGenerale'] = "Registrazione avvenuta con successo! Ora puoi accedere.";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data['valoreEmail'] = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($data['valoreEmail'])) $data['erroreEmail'] = "Inserisci l'email.";
    if (empty($password)) $data['errorePassword'] = "Inserisci la password.";

    if (!$data['erroreEmail'] && !$data['errorePassword']) {
        $utente = null;
        $tipoUtente = null;

        // Controlla admin
        $stmt = $pdo->prepare("SELECT id_admin as id, nome, cognome, email, password_hash FROM admin WHERE email = :email LIMIT 1");
        $stmt->execute([':email' => $data['valoreEmail']]);
        $admin = $stmt->fetch();

        if ($admin) {
            $utente = $admin;
            $tipoUtente = 'admin';
        } else {
            // Controlla utente
            $stmt = $pdo->prepare("SELECT id_utente as id, nome, cognome, email, password_hash FROM utente WHERE email = :email LIMIT 1");
            $stmt->execute([':email' => $data['valoreEmail']]);
            $user = $stmt->fetch();
            if ($user) {
                $utente = $user;
                $tipoUtente = 'utente';
            }
        }

        if (!$utente) {
            $data['erroreEmail'] = "Errore: Utente non trovato.";
            $data['classeEmail'] = "input-error";
        } elseif (!password_verify($password, $utente['password_hash'])) {
            $data['errorePassword'] = "Errore: Password non corretta.";
            $data['classePassword'] = "input-error";
        } else {
            // Login riuscito
            $_SESSION['user_id'] = $utente['id'];
            $_SESSION['user_email'] = $utente['email'];
            $_SESSION['user_nome'] = $utente['nome'];
            $_SESSION['user_tipo'] = $tipoUtente;
            $_SESSION['logged_in'] = true;

            if ($tipoUtente === 'admin') header("Location: dashboardAdmin.php");
            else header("Location: paginaUtente.php");
            exit;
        }
    }
}
//Generazione Logica Navbar (crea $navbarBlock)
require_once 'php/navbar.php';
//Caricamento template
$templatePath = __DIR__ . '/html/Area_login.html';

//HO TOLTO:  $navbar_html = ob_get_clean();

if (file_exists($templatePath)) {
    $template = file_get_contents($templatePath);

    // sostituzione navbar
    $template = str_replace("[navbar]", $navbarBlock, $template);

    // 4. Sostituzione dei dati dinamici del form
    foreach ($data as $key => $value) {
        // [valoreEmail], [erroreEmail], etc.
        $template = str_replace("[$key]", $value, $template);
    }

    echo $template;
} else {
    die("Errore: Template Area_login.html non trovato.");
}

?>