<?php
// src/cerca.php
session_start();
require_once 'php/connessione.php';

// 1. Includiamo la navbar per generare $navbarBlock
require_once 'php/navbar.php'; 

// 2. Recupero Query
$q = isset($_GET['q']) ? trim($_GET['q']) : '';
$risultatiHtml = '';
$messaggio = '';
$titoloPagina = 'Cerca';

if (!empty($q)) {
    $titoloPagina = "Risultati per: \"" . htmlspecialchars($q) . "\"";
    try {
        // CORREZIONE QUERY: Usiamo due placeholder diversi (:q1 e :q2)
        // per compatibilità con tutti i driver PDO.
        $sql = "SELECT * FROM prodotto 
                WHERE (nome LIKE :q1 OR descrizione LIKE :q2) 
                AND disponibilita > 0 
                ORDER BY nome ASC";
        
        $stmt = $pdo->prepare($sql);
        
        // Prepariamo il termine di ricerca con i jolly %
        $searchTerm = "%" . $q . "%";
        
        // Eseguiamo passando il valore a entrambi i placeholder
        $stmt->execute([
            ':q1' => $searchTerm,
            ':q2' => $searchTerm
        ]);
        
        $prodotti = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $numRisultati = count($prodotti);
        
        if ($numRisultati > 0) {
            $messaggio = "<p>Abbiamo trovato <strong>$numRisultati</strong> prodotti per la tua ricerca.</p>";
            
            // Generazione Card (Stile identico a catalogo.php)
            foreach ($prodotti as $row) {
                $idProdotto = $row['id_prodotto'];
                $nome = htmlspecialchars($row['nome']);
                // Tagliamo la descrizione
                $descrizione = htmlspecialchars(substr($row['descrizione'], 0, 100)); 
                $prezzo = number_format($row['prezzo'], 2);
                
                $img = !empty($row['img_path']) ? htmlspecialchars($row['img_path']) : 'images/placeholder_tea.jpg';
                
                $risultatiHtml .= <<<HTML
                <article class='product-card'>
                    <div class='product-image'>
                        <img src='$img' alt='$nome' loading='lazy'>
                    </div>
                    <h3>$nome</h3>
                    <p>$descrizione...</p>
                    <p class='product-price'>€$prezzo</p>
                    
                    <div class='product-buttons'>
                        <a href='prodotto.php?id=$idProdotto' class='bottone-primario'>Scopri di più</a>
                    </div>
                </article>
HTML;
            }
        } else {
            $messaggio = "<p>Nessun prodotto trovato per \"<strong>" . htmlspecialchars($q) . "</strong>\".<br>Prova a cercare qualcos'altro, come \"Tè Verde\" o \"Relax\".</p>";
            $risultatiHtml = ''; 
        }

    } catch (PDOException $e) {
        $messaggio = "<p style='color:red'>Si è verificato un errore tecnico durante la ricerca.</p>";
        // Log dell'errore per il debug (non visibile all'utente)
        error_log("Errore ricerca DB: " . $e->getMessage());
    }
} else {
    $messaggio = "<p>Inserisci un termine per iniziare la ricerca.</p>";
}

// 3. Caricamento Template
$templatePath = 'html/cerca.html';

if (file_exists($templatePath)) {
    $template = file_get_contents($templatePath);
    
    // Sostituzioni
    $template = str_replace('[navbar]', $navbarBlock, $template);
    $template = str_replace('[TITOLO_PAGINA]', $titoloPagina, $template);
    $template = str_replace('[MESSAGGIO_RISULTATI]', $messaggio, $template);
    $template = str_replace('[LISTA_PRODOTTI]', $risultatiHtml, $template);
    
    echo $template;
} else {
    die("Errore: Template cerca.html non trovato.");
}
?>