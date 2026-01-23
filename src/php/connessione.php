<?php
$db_host = "127.0.0.1";
$db_name = "lcasagra";
$db_username = "lcasagra";
$db_password = "Aedi1ou1gohphi9u";

// Stringa di connessione (DSN)
$charset = 'utf8mb4';
$dsn = "mysql:host=$db_host;dbname=$db_name;charset=$charset"; // CORRETTO: usare le variabili giuste

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
    // forzare UTF-8
    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
];

try {
    // Crea la connessione
    $pdo = new PDO($dsn, $db_username, $db_password, $options); // CORRETTO: usare le variabili giuste
    
} catch (\PDOException $e) {
    // In caso di errore
    die("Errore di connessione al database: " . $e->getMessage());
}
?>