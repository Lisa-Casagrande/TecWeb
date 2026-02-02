<?php
// php/salvaProdotto.php
require_once 'connessione.php';
require_once 'verificaSessioneAdmin.php';

// Funzione di pulizia base
function pulisciInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Recupero Dati
    $nome = pulisciInput($_POST['nome'] ?? '');
    $descrizione = pulisciInput($_POST['descrizione'] ?? '');
    $categoria = $_POST['categoria'] ?? '';
    $prezzo = $_POST['prezzo'] ?? '';
    $disponibilita = $_POST['disponibilita'] ?? '';
    
    // Campi opzionali: usiamo stringa vuota come default per facilitare i controlli dopo
    $grammi = !empty($_POST['grammi']) ? $_POST['grammi'] : 50; 
    $id_base = $_POST['id_base'] ?? ''; 

    // VALIDAZIONE LATO SERVER
    $errori = [];

    // Validazione Nome
    if (strlen($nome) < 2 || strlen($nome) > 100) {
        $errori[] = "Il nome deve avere tra 2 e 100 caratteri.";
    }
    // Validazione Descrizione
    if (strlen($descrizione) < 10) {
        $errori[] = "La descrizione deve essere lunga almeno 10 caratteri.";
    }
    // Validazione Categoria
    $categorie_valide = ['tè_verde', 'tè_nero', 'tè_bianco', 'tè_giallo', 'tè_oolong', 'tisana', 'infuso', 'altro'];
    if (!in_array($categoria, $categorie_valide)) {
        $errori[] = "Selezionare una categoria valida.";
    }
    // Validazione Prezzo
    if (!is_numeric($prezzo) || $prezzo < 0) {
        $errori[] = "Inserire un prezzo valido (usa il punto per i decimali, es. 10.50).";
    }
    // Validazione Disponibilità
    if (!ctype_digit((string)$disponibilita)) {
        $errori[] = "Inserire un numero intero positivo per la disponibilità.";
    }
    // Validazione ID_BASE
    if ($id_base !== '' && !ctype_digit((string)$id_base)) {
        $errori[] = "Selezionare una base valida.";
    }
    // Validazione Grammi
    if ($grammi !== '' && !ctype_digit((string)$grammi)) {
        $errori[] = "Inserire un numero intero positivo (grammi).";
    }
    // Ferma tutto se ci sono errori
    if (!empty($errori)) {
        $msg = urlencode(implode(" - ", $errori));
        header("Location: ../aggiungiProdotto.php?error=" . $msg);
        exit;
    }

    // Gestione Immagine
    $img_path_db = null; 

    if (isset($_FILES['img_path']) && $_FILES['img_path']['error'] === UPLOAD_ERR_OK && $_FILES['img_path']['size'] > 0) {

        // Controllo dimensioni
        if ($_FILES['img_path']['size'] > 2097152) {
            header("Location: ../aggiungiProdotto.php?error=Il file non è troppo grande");
            exit;
        }

        // Controllo che sia una vera immagine
        $check = getimagesize($_FILES["img_path"]["tmp_name"]);
        if ($check === false) {
            header("Location: ../aggiungiProdotto.php?error=Il file non è una immagine valida");
            exit;
        }

        // Controllo MIME Type
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $realMimeType = finfo_file($finfo, $_FILES['img_path']['tmp_name']);
        finfo_close($finfo);

        $allowedMimeTypes = ['image/jpeg', 'image/jpg', 'image/webp'];
        if (!in_array($realMimeType, $allowedMimeTypes)) {
            header("Location: ../aggiungiProdotto.php?error=Formato non consentito.");
            exit;
        }

        // Genera nome file univoco per evitare sovrascritture 
        $estensione = pathinfo($_FILES["img_path"]["name"], PATHINFO_EXTENSION);
        $nuovoNomeFile = md5(time() . $_FILES["img_path"]["name"]) . "." . $estensione;
        
        $target_dir = "../images/prodotti/";
        $target_file = $target_dir . $nuovoNomeFile;
        $img_path_db = "images/prodotti/" . $nuovoNomeFile; // Path per il DB

        if (!move_uploaded_file($_FILES["img_path"]["tmp_name"], $target_file)) {
            header("Location: ../aggiungiProdotto.php?error=Errore tecnico nel caricamento immagine");
            exit;
        }
    }

    // Preparazione Dati per DB (stringhe vuote diventano null per campi opzionali)
    $grammiFinale = ($grammi === '') ? null : $grammi;
    $idBaseFinale = ($id_base === '') ? null : $id_base;

    // Inserimento nel database
    try {
        $sql = "INSERT INTO prodotto (nome, descrizione, prezzo, grammi, categoria, img_path, disponibilita, id_base) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $pdo->prepare($sql);
        
        $stmt->execute([
            $nome, 
            $descrizione, 
            $prezzo, 
            $grammiFinale, 
            $categoria, 
            $img_path_db, 
            $disponibilita,
            $idBaseFinale
        ]);

        header("Location: ../gestioneProdotti.php?msg=success");
        exit;

    } catch (PDOException $e) {
        die("Errore Database: " . $e->getMessage());
    }

} else {
    header("Location: ../aggiungiProdotto.php");
    exit;
}
?>