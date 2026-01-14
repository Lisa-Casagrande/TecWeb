<?php
// Includi la connessione al database
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'php/connessione.php';

//logica della Navbar: includendo questo file, viene generata la variabile $navbarBlock
require_once 'php/navbar.php';

//variabili per il template
$prodottiPiuAmati = '';
$erroreProdotti = false;

try {
    // Query per ottenere i 3 prodotti più venduti
    $sql = "SELECT 
            p.id_prodotto,
            p.nome,
            p.descrizione,
            p.prezzo,
            p.img_path,
            SUM(do.quantita) as totale_vendite,
            GROUP_CONCAT(DISTINCT i.nome SEPARATOR ', ') as lista_ingredienti,
            b.nome as nome_base
            FROM prodotto p
            LEFT JOIN dettaglio_ordine do ON p.id_prodotto = do.id_prodotto
            LEFT JOIN prodotto_ingrediente pi ON p.id_prodotto = pi.id_prodotto
            LEFT JOIN ingrediente i ON pi.id_ingrediente = i.id_ingrediente
            LEFT JOIN base b ON p.id_base = b.id_base
            LEFT JOIN ordine o ON do.id_ordine = o.id_ordine
            WHERE o.stato_ord IN ('pagato', 'spedito', 'consegnato') 
            OR o.stato_ord IS NULL
            GROUP BY p.id_prodotto
            ORDER BY totale_vendite DESC, p.nome ASC
            LIMIT 3";
    
    $stmt = $pdo->query($sql);
    $prodotti = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (count($prodotti) > 0) {
        foreach ($prodotti as $row) {
            $nome = htmlspecialchars($row['nome'], ENT_QUOTES, 'UTF-8');
            $prezzo = number_format($row['prezzo'], 2, ',', '.');
            $img = htmlspecialchars($row['img_path'] ?? '', ENT_QUOTES, 'UTF-8');
            $descrizione = htmlspecialchars(substr($row['descrizione'] ?? '', 0, 90), ENT_QUOTES, 'UTF-8');
            $idProdotto = $row['id_prodotto'];
            $imgAlt = htmlspecialchars($row['nome'], ENT_QUOTES, 'UTF-8');
            $immagineProdotto = !empty($img) ? $img : 'images/placeholder_tea.jpg';
            
            $prodottiPiuAmati .= "
            <article class='product-card'>
                <div class='product-image'>
                    <img src='$immagineProdotto' alt='$imgAlt' loading='lazy' onerror=\"this.src='images/placeholder_tea.jpg'\">
                </div>
                <h3>$nome</h3>
                <p>$descrizione...</p>
                <p class='product-price'>€$prezzo</p>
                <a href='prodotto.php?id=$idProdotto' class='bottone-primario'>Scopri di più</a>
            </article>
            ";
        }
    } else {
        // Fallback: mostra 3 prodotti fissi se non ci sono vendite
        $sqlFallback = "SELECT p.*, 
                            GROUP_CONCAT(DISTINCT i.nome SEPARATOR ', ') as lista_ingredienti,
                            b.nome as nome_base
                        FROM prodotto p
                        LEFT JOIN prodotto_ingrediente pi ON p.id_prodotto = pi.id_prodotto
                        LEFT JOIN ingrediente i ON pi.id_ingrediente = i.id_ingrediente
                        LEFT JOIN base b ON p.id_base = b.id_base
                        WHERE p.disponibilita > 0
                        GROUP BY p.id_prodotto
                        ORDER BY p.prezzo DESC, p.nome ASC
                        LIMIT 3";
        
        $stmtFallback = $pdo->query($sqlFallback);
        
        if ($stmtFallback->rowCount() > 0) {
            while ($row = $stmtFallback->fetch()) {
                $nome = htmlspecialchars($row['nome'], ENT_QUOTES, 'UTF-8');
                $prezzo = number_format($row['prezzo'], 2, ',', '.');
                $img = htmlspecialchars($row['img_path'] ?? '', ENT_QUOTES, 'UTF-8');
                $descrizione = htmlspecialchars(substr($row['descrizione'] ?? '', 0, 90), ENT_QUOTES, 'UTF-8');
                $idProdotto = $row['id_prodotto'];
                $imgAlt = htmlspecialchars($row['nome'], ENT_QUOTES, 'UTF-8');
                $immagineProdotto = !empty($img) ? $img : 'images/placeholder_tea.jpg';
                
                $prodottiPiuAmati .= "
                <article class='product-card'>
                    <div class='product-image'>
                        <img src='$immagineProdotto' alt='$imgAlt' loading='lazy' onerror=\"this.src='images/placeholder_tea.jpg'\">
                    </div>
                    <h3>$nome</h3>
                    <p>$descrizione...</p>
                    <p class='product-price'>€$prezzo</p>
                    <a href='prodotto.php?id=$idProdotto' class='bottone-primario'>Scopri di più</a>
                </article>
                ";
            }
        } else {
            $prodottiPiuAmati = '<p style="text-align:center; grid-column: 1 / -1; padding: 20px;">Nessun prodotto disponibile al momento. Torna a trovarci presto!</p>';
        }
    }
    
} catch (PDOException $e) {
    error_log("Errore index.php: " . $e->getMessage());
    $erroreProdotti = true;
    $prodottiPiuAmati = '<p>Impossibile caricare i prodotti in questo momento.</p>';
}

// Caricamento del Template Principale
$templateIndex = file_get_contents('html/index.html');

// Sostituzione dei Placeholder
$paginaFinale = str_replace(
    ['[navbar]', '[prodottiPiuAmati]'],
    [$navbarBlock, $prodottiPiuAmati],
    $templateIndex
);

//output finale
echo $paginaFinale;
?>