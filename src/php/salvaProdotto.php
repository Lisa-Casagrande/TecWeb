<?php
// collegamento al DB per salvare il nuovo prodotto creato con la form aggiungiProdotto.php
require_once 'connessione.php';
require_once 'verificaSessioneAdmin.php';

// Funzione di pulizia base pe input utente
function pulisciInput($data) {
    $data = trim($data); //toglie spazi vuoti
    $data = stripslashes($data); //rimuove backslash
    $data = htmlspecialchars($data); //converte caratteri speciali in entità html -> impedisce attacchi che inseriscono codice JS malevolo nel nome prodotto
    return $data;
}

//CONTROLLA CHE LA PAGINA SIA CHIAMATA TRAMITE METODO POST DA CLICK SUL BOTTONE "Inserisci Prodotto"
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // 1. Recupera i dati dal form (usando name) e chiama pulisciInput
    $nome = pulisciInput($_POST['nome']);
    $descrizione = pulisciInput($_POST['descrizione']);
    $categoria = $_POST['categoria']; //no pulisci input perchè sono opzioni date
    $prezzo = $_POST['prezzo']; //numeri
    $disponibilita = $_POST['disponibilita'];
    $grammi = $_POST['grammi'];
    // Se l'utente lascia "-- Seleziona --", il value è vuoto = nel DB deve diventare NULL
    $id_base = !empty($_POST['id_base']) ? $_POST['id_base'] : null;

    //2. Gestione immagine
    $img_path_db = null; //default: nessun percorso
    // controlla se è stato inviato un file per l'immagine (isset) e se non ci sono stati errori
    if (isset($_FILES['img_path']) && $_FILES['img_path']['error'] === 0 && $_FILES['img_path']['size'] > 0) {

        // SICUREZZA 1: CONTROLLO DIMENSIONI (Max 2MB = 2 * 1024 * 1024 bytes)
        if ($_FILES['img_path']['size'] > 2097152) {
            header("Location: ../aggiungiProdotto.php?error=Il file non è troppo grande"); //mostra messaggio errore
            exit;
        }
        // SICUREZZA 2: CONTROLLO SE È VERA IMMAGINE (getimagesize: legge dimensioni)
        $check = getimagesize($_FILES["img_path"]["tmp_name"]);
        if ($check === false) {
            header("Location: ../aggiungiProdotto.php?error=Il file non è una immagine valida");
            exit;
        }
        // SICUREZZA 3: CONTROLLO MIME TYPE REALE: controlla il contenuto del file
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $realMimeType = finfo_file($finfo, $_FILES['img_path']['tmp_name']);
        finfo_close($finfo);

        // controllo formati accettati
        $allowedMimeTypes = [
            'image/jpeg' => 'jpg',
            'image/webp' => 'webp'
        ];
        if (!array_key_exists($realMimeType, $allowedMimeTypes)) {
            header("Location: ../aggiungiProdotto.php?error=Formato non consentito.");
            exit;
        }

        // Salvataggio
        $fileName = basename($_FILES["img_path"]["name"]); //pulisce nome file
        $target_dir = "../images/prodotti/"; //cartella di destinazione
        $img_path_db = "images/prodotti/" . $fileName; //percorso dal salvare nel DB
        $target_file = $target_dir . $fileName;

        // Ultimo controllo estensione (per coerenza col nome file)
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $allowedExtensions = ['jpg', 'jpeg', 'webp']; 
        if (!in_array($imageFileType, $allowedExtensions)) {
             header("Location: ../aggiungiProdotto.php?error=Estensione non valida (solo JPG, WEBP)");
             exit;
        }

        // Spostamento file
        if (!move_uploaded_file($_FILES["img_path"]["tmp_name"], $target_file)) {
            header("Location: ../aggiungiProdotto.php?error=Errore tecnico nel caricamento immagine");
            exit;
        }
    }

    // 3. Inserimento nel database
    try {
        //punti di domanda invece di mettere direttamente le variabili per sicurezza; dopo vengono inseriti i dati sicuri
        $sql = "INSERT INTO prodotto (nome, descrizione, prezzo, grammi, categoria, img_path, disponibilita, id_base) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $pdo->prepare($sql);
        
        $stmt->execute([
            $nome, 
            $descrizione, 
            $prezzo, 
            $grammi, 
            $categoria, 
            $img_path_db, 
            $disponibilita,
            $id_base
        ]);

        //successo: reindirizza admin alla lista di prodotti
        header("Location: ../gestioneProdotti.php");
        exit;

    } catch (PDOException $e) {
        // gestione errori DB: cattura eccezione e stampa errore
        die("Errore Database: " . $e->getMessage());
    }

} else {
    //se si tenta di accedere senza POST esce e torna nella pagina della form
    header("Location: ../aggiungiProdotto.php");
    exit;
}
?>