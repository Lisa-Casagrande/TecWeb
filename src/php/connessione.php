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
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
    // forzare UTF-8
    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
];

try {
    // Crea la connessione
    $pdo = new PDO($dsn, $user, $pass, $options);
    
    // Imposta esplicitamente il charset della connessione (ridondante ma sicuro)
    $pdo->exec("SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("SET CHARACTER SET utf8mb4");
    
} catch (\PDOException $e) {
    // In caso di errore
    die("Errore di connessione al database: " . $e->getMessage());
}
?>