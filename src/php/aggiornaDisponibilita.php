<?php
// file per logica php dietro all'aggiornamento delle disponibilità di ingredienti e basi (salvato in php/)
// piccolo form che permette di aumentare o diminuire le quantità disponibili - gestioneIngredienti.php
// Gestisce l'aggiornamento della colonna 'disponibile' per basi e ingredienti

require_once 'connessione.php';
require_once 'verificaSessioneAdmin.php';

// Controlla che la richiesta sia arrivata tramite metodo POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Recupero e Sanificazione Dati - intval per assicurare che ID e Quantità sono numeri interi
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $tipo = isset($_POST['tipo']) ? $_POST['tipo'] : '';
    $quantita = isset($_POST['quantita']) ? intval($_POST['quantita']) : 0;

    // Impedisce l'inserimento di valori negativi nel magazzino
    if ($quantita < 0) {
        $quantita = 0;
    }

    // Selezione della Tabella del DB: o base o ingrediente - definisco parametri in base al tipo (base o ingrediente) per riutilizzare la query
    if ($tipo === 'base') {
        $tabella = 'base';
        $colonna_id = 'id_base';
    } elseif ($tipo === 'ingrediente') {
        $tabella = 'ingrediente';
        $colonna_id = 'id_ingrediente';
    } else {
        //se il tipo non è valido, reindirizza con errore
        header("Location: ../gestioneIngredienti.php?error=invalid_type");
        exit;
    }

    // Aggiornamento del Database
    try {
        // query per evitare SQL Injection
        $sql = "UPDATE $tabella SET disponibile = ? WHERE $colonna_id = ?";
        $stmt = $pdo->prepare($sql);
        
        // passo dati sanificati
        $stmt->execute([$quantita, $id]);

        // Se successo - torna alla pagina di gestioneIngredienti con messaggio di successo nell'url
        header("Location: ../gestioneIngredienti.php?msg=success");
        exit;

    } catch (PDOException $e) {
        // gestione errori nel database
        die("Errore durante l'aggiornamento della disponibilità: " . $e->getMessage());
    }

} else {
    // Se si tenta di accedere al file direttamente tramite GET, riporta alla dashboard
    header("Location: ../gestioneIngredienti.php");
    exit;
}
?>