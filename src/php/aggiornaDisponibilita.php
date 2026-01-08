<?php
// file per logica php dietro all'aggiornamento delle disponibilità di ingredienti (salvato in php/)
// piccolo form che permette di aumentare o diminuire le quantità disponibili - gestioneIngredienti.php
// Gestisce l'aggiornamento della colonna 'disponibile' per gli ingredienti
require_once 'connessione.php';
require_once 'verificaSessioneAdmin.php';

// Controlla che la richiesta sia arrivata tramite metodo POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    //recupero e sanificazione Dati - intval per assicurare che ID e Quantità sono numeri interi
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $tipo = isset($_POST['tipo']) ? $_POST['tipo'] : '';
    $quantita = isset($_POST['quantita']) ? intval($_POST['quantita']) : 0;

    // impedisce inserimento valori negativi
    if ($quantita < 0) {
        $quantita = 0;
    }
    // selezione tabella ingrediente
    if ($tipo === 'ingrediente') {
        $tabella = 'ingrediente';
        $colonna_id = 'id_ingrediente';
    } else {
        // Se si prova a modificare una base (che non ha disponibilità) o altro tipo
        header("Location: ../gestioneIngredienti.php?error=invalid_type");
        exit;
    }
    // Aggiornamento del Database
    try {
        $sql = "UPDATE $tabella SET disponibile = ? WHERE $colonna_id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$quantita, $id]);
        header("Location: ../gestioneIngredienti.php?msg=success");
        exit;

    } catch (PDOException $e) {
        die("Errore durante l'aggiornamento della disponibilità: " . $e->getMessage());
    }
} else {
    header("Location: ../gestioneIngredienti.php");
    exit;
}
?>