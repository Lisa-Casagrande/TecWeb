<?php
// Configurazione database
define('DB_HOST', 'db');
define('DB_NAME', 'db_InfuseMe');
define('DB_USER', 'infuseme_user');
define('DB_PASSWORD', 'InfuseMe123!');

function getPDOConnection() {
    try {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
        $pdo = new PDO($dsn, DB_USER, DB_PASSWORD, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]);
        return $pdo;
    } catch (PDOException $e) {
        die("Errore di connessione al database: " . $e->getMessage());
    }
}
?>
