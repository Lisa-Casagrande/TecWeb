<?php
//connessione e controllo sicurezza
require_once 'connessione.php';
require_once 'verificaSessioneAdmin.php';

//controlla che la richiesta sia arrivata tramite metodo POST (no GET) e che ci sia l'ID del prodotto da eliminare
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id_prodotto'])) {
    
    $id = $_POST['id_prodotto'];
    
    try {
        //query sql per cancellare il prodotto dal db: usa prepare e ? per evitare SQL injection
        $stmt = $pdo->prepare("DELETE FROM prodotto WHERE id_prodotto = ?");
        //esegue query passando l'ID (sostituisce il ?)
        $stmt->execute([$id]);
        
        //se è andato bene, torna alla pagina di gestione prodotti
        header("Location: ../gestioneProdotti.php");
        exit;
        
    } catch (PDOException $e) {
        //in caso di errore (es. vincoli di integrità referenziale se il prodotto è in un ordine)
        die("Errore durante l'eliminazione del prodotto: " . $e->getMessage());
    }

} else {
    //se qualcuno prova ad aprire questa pagina direttamente senza inviare dati viene mandato indietro
    header("Location: ../gestioneProdotti.php");
    exit;
}

// da pagina gestioneProdotti.php c'è un pulsante elimina: manda i dati a questo file che elimina il prodotto e ti rimanda alla pagina

?>