<?php
// Configurazione per Docker
$host = 'db'; // Nome del servizio nel docker-compose
$db   = 'db_InfuseMe';
$user = 'infuseme_user';
$pass = 'InfuseMe123!';

// Stringa di connessione (DSN)
$charset = 'utf8mb4';
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Lancia errori se qualcosa non va
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,      // Restituisce array associativi
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    // Crea la connessione
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    // In caso di errore
    die("Errore di connessione al database: " . $e->getMessage());
}
?>
