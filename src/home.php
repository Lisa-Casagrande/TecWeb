<?php
// Includi la connessione al database
require_once 'php/connessione.php';

$prodottiHtml = '';
try {
    // Query per ottenere 3 prodotti a caso
    $sql = "SELECT p.*, 
                   GROUP_CONCAT(DISTINCT i.nome SEPARATOR ', ') as lista_ingredienti,
                   b.nome as nome_base
            FROM prodotto p
            LEFT JOIN prodotto_ingrediente pi ON p.id_prodotto = pi.id_prodotto
            LEFT JOIN ingrediente i ON pi.id_ingrediente = i.id_ingrediente
            LEFT JOIN base b ON p.id_base = b.id_base
            GROUP BY p.id_prodotto
            ORDER BY RAND() 
            LIMIT 3";
    
    $stmt = $pdo->query($sql);
    
    while ($row = $stmt->fetch()) {
        $nome = htmlspecialchars($row['nome'], ENT_QUOTES, 'UTF-8');
        $prezzo = number_format($row['prezzo'], 2, ',', '.');
        $img = htmlspecialchars($row['img_path'], ENT_QUOTES, 'UTF-8');
        $descrizione = htmlspecialchars(substr($row['descrizione'], 0, 90), ENT_QUOTES, 'UTF-8');
        $idProdotto = $row['id_prodotto'];
        $imgAlt = htmlspecialchars($row['nome'], ENT_QUOTES, 'UTF-8');
        
        // Gestisci l'immagine di fallback se il percorso è vuoto
        $immagineProdotto = !empty($img) ? $img : 'images/placeholder_tea.jpg';
        
        $prodottiHtml .= '
        <article class="product-card">
            <div class="product-image">
                <img src="' . $immagineProdotto . '" alt="' . $imgAlt . '" loading="lazy" onerror="this.src=\'images/placeholder_tea.jpg\'">
            </div>
            <h3>' . $nome . '</h3>
            <p>' . $descrizione . '...</p>
            <p class="product-price">€' . $prezzo . '</p>
            <a href="prodotto.php?id=' . $idProdotto . '" class="bottone-primario">Scopri di più</a>
        </article>';
    }
    
} catch (PDOException $e) {
    // Se c'è un errore, mostra un messaggio semplice
    error_log("Errore home.php: " . $e->getMessage());
    $prodottiHtml = '<p style="text-align:center; grid-column: 1 / -1;">Impossibile caricare i prodotti in questo momento.</p>';
}

// Se non ci sono prodotti
if (empty($prodottiHtml)) {
    $prodottiHtml = '<p style="text-align:center; grid-column: 1 / -1;">Nessun prodotto disponibile al momento.</p>';
}
?>

<!DOCTYPE html>
<html lang="it" xml:lang="it" xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0" />
    <title>InfuseMe: Infusi, tè e tisane artigianali dal 1975</title>
    <meta name="description" content="InfuseMe - Tè e infusi artigianali dal 1975. Qualità, tradizione e sostenibilità dalla Val d'Ossola." />
    <meta name="keywords" content="tè, infusi, tisane, biologico, artigianale, Val d'Ossola, blend" />
    <link rel="stylesheet" href="style.css" type="text/css" />
</head>

<body>
<a href="#main-content" class="skip-link">Salta al contenuto principale</a>
    <?php include 'navbar.php'; ?>

    <!-- Main Content -->
    <main id="main-content" role="main">

        <!-- Hero Section con sfondo immagine -->
        <section class="hero" aria-labelledby="hero-title">
            <img src="images/hero/heroHome.jpg" alt="" class="hero-background" aria-hidden="true">
            <div class="hero-content">
                <h1 id="hero-title"><span lang="en">InfuseMe</span>: Tè e infusi artigianali nati tra le montagne della Val d'Ossola</h1>
                <p class="hero-subtitle">La qualità è una tradizione che coltiviamo dal 1975</p>
                <div class="hero-buttons">
                    <a href="chiSiamo.html" class="bottone-primario">Scopri la nostra storia</a>
                </div>
            </div>
        </section>

        <!-- Contenuto Principale -->
        <section class="about-preview" aria-labelledby="about-title">
            <div class="container">
                <h2 id="about-title">La nostra filosofia</h2>
                <div>
                    <p>Nati nel cuore della Val d'Ossola, coltiviamo la passione per tè e infusi da oltre quarant'anni. Dal 1975, trasformiamo ingredienti semplici in momenti di benessere quotidiano, selezionando da generazioni foglie, erbe e fiori con la pazienza di chi conosce il valore del tempo e della natura.</p>
                    
                    <p>Ogni miscela <span lang="en">InfuseMe</span> nasce da ingredienti selezionati e processi artigianali che rispettano la natura e i suoi tempi. Crediamo in un'infusione lenta, capace di raccontare la storia di ogni ingrediente, perché il benessere comincia dai piccoli dettagli che curiamo selezionando ogni foglia a mano.</p>
                    
                    <p>Ogni tazza <span lang="en">InfuseMe</span> è il nostro invito a rallentare, respirare e ritrovare il tuo equilibrio.</p>
                </div>
            </div>
        </section>

        <!-- Featured Products -->
        <section class="featured-products" aria-labelledby="featured-title">
            <div class="container">
                <h2 id="featured-title">I più amati dai nostri clienti</h2>
                <div class="products-grid">
                    <?php
                    // Includi la connessione al database
                    require_once 'php/connessione.php';
                    
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
                                $totaleVendite = $row['totale_vendite'] ?? 0;
                                
                                // Gestisci l'immagine di fallback se il percorso è vuoto
                                $immagineProdotto = !empty($img) ? $img : 'images/placeholder_tea.jpg';
                                
                                echo "
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
                            // Se non ci sono ancora vendite, mostra 3 prodotti fissi (non casuali)
                            // Possiamo mostrare i più recenti o quelli con prezzo maggiore, o semplicemente i primi 3
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
                                    
                                    echo "
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
                                // Se non ci sono prodotti nel database, mostra un messaggio
                                echo '<p style="text-align:center; grid-column: 1 / -1; padding: 20px;">
                                        Nessun prodotto disponibile al momento. Torna a trovarci presto!
                                    </p>';
                            }
                        }
                        
                    } catch (PDOException $e) {
                        // Se c'è un errore, mostra un messaggio semplice
                        error_log("Errore home.php: " . $e->getMessage());
                        echo '<p style="text-align:center; grid-column: 1 / -1; padding: 20px; color: #666;">
                                Impossibile caricare i prodotti in questo momento.
                            </p>';
                    }
                    ?>
                </div>
                <div>
                    <a href="catalogo.php" class="bottone-primario">Esplora il catalogo</a>
                </div>
            </div>
        </section>

        <!-- Slogan Section -->
        <section class="slogan-section" aria-label="Motto dell'azienda">
            <div class="container">
                <p class="slogan">Assapora la natura, un sorso alla volta</p>
            </div>
        </section>

        <!-- Values Section -->
        <section class="values-section" aria-labelledby="values-title">
            <div class="container">
                <h2 id="values-title" class="sr-only">I nostri valori</h2>
                <div class="values-grid">

                    <!--icona foglia per ingredienti naturali-->
                   <div class="value-item">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M23.119.872A2.985,2.985,0,0,0,20.714.015C17.921.285,8.528,1.448,4.9,5.072a9.931,9.931,0,0,0-.671,13.281l-3.94,3.94a1,1,0,0,0,1.414,1.414l3.94-3.94A9.929,9.929,0,0,0,18.928,19.1c3.676-3.677,4.8-13.041,5.059-15.823A2.987,2.987,0,0,0,23.119.872Zm-5.6,16.81a7.925,7.925,0,0,1-10.439.657l9.632-9.632a1,1,0,0,0-1.414-1.414L5.661,16.925A7.924,7.924,0,0,1,6.318,6.486C8.827,3.978,15.745,2.5,20.907,2.005A1,1,0,0,1,22,3.088C21.5,8.475,20.059,15.137,17.514,17.682Z"/>
                        </svg>
                        <h3>Ingredienti naturali</h3>
                        <p>100% selezionati da fornitori locali e internazionali certificati</p>
                    </div>
                    
                    <!--icona mano con cuore per artigianalità-->
                    <div class="value-item">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M8.965,24H4a4,4,0,0,1-4-4V15a4,4,0,0,1,4-4h8.857a3.144,3.144,0,0,1,2.69,1.519l3.217-3.535a3.01,3.01,0,0,1,4.254-.2,3.022,3.022,0,0,1,.217,4.23l-6.8,7.637A10.012,10.012,0,0,1,8.965,24ZM4,13a2,2,0,0,0-2,2v5a2,2,0,0,0,2,2H8.965a8.005,8.005,0,0,0,5.972-2.678l6.805-7.638a1.015,1.015,0,0,0-.072-1.421A1.029,1.029,0,0,0,20.942,10a1,1,0,0,0-.7.329L15.816,15.2A3.158,3.158,0,0,1,13.3,17.252l-5.161.738a1,1,0,0,1-.284-1.98l5.162-.737A1.142,1.142,0,0,0,12.857,13Zm7-3.926a1.986,1.986,0,0,1-1.247-.436C8.041,7.264,6,5.2,6,3.2A3.109,3.109,0,0,1,9,0a2.884,2.884,0,0,1,2,.817A2.884,2.885,0,0,1,13,0a3.109,3.109,0,0,1,3,3.2c0,2-2.041,4.064-3.754,5.439A1.986,1.986,0,0,1,11,9.074ZM9,2A1.115,1.115,0,0,0,8,3.2c0,.9,1.151,2.39,3.006,3.879C12.849,5.59,14,4.1,14,3.2A1.115,1.115,0,0,0,13,2a1.115,1.115,0,0,0-1,1.2,1,1,0,0,1-2,0A1.115,1.115,0,0,0,9,2Z"/>
                        </svg>
                        <h3>Artigianalità piemontese</h3>
                        <p>Misceliamo ogni <span lang="en">blend</span> a mano nella nostra sede in Val d'Ossola</p>
                    </div>

                    <!--icona sostenibilità-->
                    <div class="value-item">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true">
                            <path d="m5.648,5.958c.733.22,1.521.831,2.354,1.478,1.245.967,2.655,2.064,4.323,2.064,1.965,0,3.643-1.242,4.333-2.992.545.343,1.069.79,1.56,1.405.198.247.488.375.782.375.219,0,.439-.072.624-.219.431-.345.501-.974.156-1.405-.876-1.094-1.831-1.812-2.818-2.283C16.777,1.931,14.781-.01,12.325-.01,8.5-.01,5.821,2.131,4.843,3.396c-.331.426-.43.99-.264,1.506.163.509.562.904,1.069,1.056Zm6.677-3.968c1.117,0,2.073.71,2.473,1.713-.951-.17-1.898-.203-2.798-.203-.553,0-1,.448-1,1s.447,1,1,1c.96,0,1.902.044,2.808.254-.392,1.02-1.355,1.746-2.483,1.746-.981,0-2.009-.798-3.097-1.643-.783-.609-1.59-1.236-2.468-1.617,1.005-1.017,3.054-2.25,5.564-2.25Zm-3.235,12.814c-.533-1.176-1.489-2.067-2.69-2.511-.647-.239-1.322-.319-1.99-.267.176-.48.417-.983.736-1.503.289-.471.141-1.086-.33-1.375-.472-.29-1.087-.141-1.375.33-.726,1.184-1.147,2.31-1.285,3.335-1.922,1.285-2.701,3.825-1.711,6.006,1.751,3.858,5.895,4.919,7.122,5.152.103.02.205.029.307.029.442,0,.867-.181,1.176-.51.395-.421.543-1.012.396-1.575-.065-.26-.101-.52-.104-.776-.008-.585.055-1.19.122-1.83.153-1.472.312-2.995-.374-4.504Zm-1.615,4.297c-.07.669-.143,1.361-.133,2.062.003.239.022.48.06.722-1.341-.344-3.971-1.327-5.136-3.893-.424-.935-.292-1.979.242-2.77.309.689.837,1.536,1.786,2.485.195.195.451.293.707.293s.512-.098.707-.293c.391-.391.391-1.023,0-1.414-.978-.978-1.363-1.762-1.516-2.23.19-.042.382-.063.575-.063.317,0,.635.057.94.169.696.257,1.252.777,1.563,1.462.461,1.016.337,2.208.205,3.47Zm15.431-7.166h0c-.23-.489-.684-.831-1.214-.915-.547-.084-1.109.108-1.496.521-.556.592-1.321,1.124-2.132,1.686-1.309.908-2.661,1.847-3.455,3.25-.62,1.097-.773,2.368-.431,3.579.149.528.399,1.006.709,1.44-.584.251-1.244.432-1.99.509-.55.057-.948.549-.891,1.098.053.514.487.896.993.896,1.37-.017,2.537-.543,3.48-1.081.722.391,1.501.579,2.272.579,1.669,0,3.291-.869,4.162-2.409,1.851-3.27.865-7.292-.008-9.154Zm-1.732,8.169c-.579,1.025-1.732,1.526-2.844,1.353.848-.918,1.428-1.964,1.73-2.963.16-.528-.139-1.086-.668-1.246-.531-.16-1.086.139-1.246.668-.257.85-.782,1.751-1.589,2.496-.204-.267-.361-.569-.455-.901-.195-.693-.108-1.421.247-2.049.569-1.006,1.68-1.776,2.855-2.592.72-.5,1.46-1.014,2.094-1.605.609,1.593,1.181,4.532-.125,6.84Z"/>
                        </svg>
                        <h3>Sostenibilità</h3>
                        <p><span lang="en">Packaging</span> riciclabile e filiera controllata</p>
                    </div>

                    <!--icona furgoncino per spedizione rapida-->
                    <div class="value-item">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true">
                            <path d="m19,5h-2.101c-.465-2.279-2.484-4-4.899-4h-7C2.243,1,0,3.243,0,6v9c0,1.881,1.309,3.452,3.061,3.877-.038.204-.061.412-.061.623,0,1.93,1.57,3.5,3.5,3.5s3.5-1.57,3.5-3.5c0-.169-.017-.335-.041-.5h4.082c-.024.165-.041.331-.041.5,0,1.93,1.57,3.5,3.5,3.5s3.5-1.57,3.5-3.5c0-.211-.023-.419-.061-.623,1.752-.425,3.061-1.996,3.061-3.877v-5c0-2.757-2.243-5-5-5Zm3,5v1h-5v-4h2c1.654,0,3,1.346,3,3ZM2,15V6c0-1.654,1.346-3,3-3h7c1.654,0,3,1.346,3,3v11H4c-1.103,0-2-.897-2-2Zm6,4.5c0,.827-.673,1.5-1.5,1.5s-1.5-.673-1.5-1.5c0-.19.039-.356.093-.5h2.814c.054.144.093.31.093.5Zm9.5,1.5c-.827,0-1.5-.673-1.5-1.5,0-.19.039-.356.093-.5h2.814c.054.144.093.31.093.5,0,.827-.673,1.5-1.5,1.5Zm2.5-4h-3v-4h5v2c0,1.103-.897,2-2,2Zm-15.707-6.192c-.391-.391-.391-1.023,0-1.414s1.023-.391,1.414,0l1.402,1.402c.346.346.91.346,1.256,0l2.919-2.995c.386-.395,1.021-.402,1.414-.018.396.386.403,1.019.018,1.415l-2.928,3.003c-.568.568-1.312.852-2.054.852s-1.478-.281-2.039-.843l-1.402-1.402Z"/>
                        </svg>
                        <h3>Spedizione rapida</h3>
                        <p>Consegne in 48 ore in tutta Italia</p>
                    </div>

                    <!--icona regalo per omaggio-->
                    <div class="value-item">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M22.249,10.442c.641-.729,.892-1.729,.671-2.675h0s-.083-.343-.083-.343c-.297-1.167-1.3-2.057-2.497-2.215-.82-.108-1.653-.2-2.495-.275,.02-.15,.03-.302,.03-.455,0-1.896-1.542-3.438-3.438-3.438-.952,0-1.814,.389-2.438,1.016-.623-.627-1.486-1.016-2.438-1.016-1.896,0-3.438,1.542-3.438,3.438,0,.154,.011,.307,.031,.458-.843,.076-1.677,.168-2.498,.277-1.197,.159-2.2,1.05-2.495,2.216l-.083,.342c-.22,.947,.031,1.947,.672,2.676,.126,.144,.263,.273,.409,.387-.607,3.072-.497,6.583,.301,9.447,.318,1.137,1.278,1.984,2.448,2.156,2.352,.346,4.721,.518,7.089,.518s4.738-.173,7.09-.518c1.169-.172,2.13-1.019,2.447-2.156,.799-2.865,.908-6.377,.302-9.452,.146-.114,.284-.244,.41-.388Zm-2.171-3.251c.394,.052,.723,.343,.82,.729l.074,.299h0c.075,.325-.007,.654-.226,.903-.214,.242-.507,.365-.842,.331-2.262-.228-4.58-.355-6.905-.383v-2.385c2.401,.036,4.791,.205,7.078,.507ZM14.438,3.042c.793,0,1.438,.645,1.438,1.438,0,.089-.001,.213-.015,.309-.942-.054-1.891-.089-2.843-.103-.008-.067-.017-.151-.017-.206,0-.792,.645-1.438,1.438-1.438Zm-4.875,0c.792,0,1.438,.645,1.438,1.438,0,.055-.009,.14-.017,.207-.951,.015-1.901,.05-2.843,.105-.014-.098-.015-.223-.015-.312,0-.792,.645-1.438,1.438-1.438ZM3.027,8.226l.074-.304c.097-.381,.426-.673,.819-.725,2.288-.303,4.678-.474,7.08-.512v2.385c-2.326,.029-4.645,.159-6.906,.388-.327,.037-.628-.089-.841-.331-.219-.249-.301-.577-.226-.901Zm1.362,11.519c-.687-2.464-.8-5.593-.311-8.284,.072-.002,.145-.006,.218-.013,2.196-.223,4.447-.348,6.705-.378v9.875c-1.938-.042-3.874-.203-5.799-.486-.388-.057-.707-.338-.812-.715Zm15.222,0c-.104,.372-.431,.659-.812,.715-1.924,.283-3.861,.444-5.799,.486V11.069c2.258,.028,4.509,.152,6.705,.373,.072,.007,.144,.011,.216,.013,.49,2.693,.378,5.824-.309,8.288Z"/>
                        </svg>
                        <h3>Omaggio</h3>
                        <p>Un regalo omaggio per te dopo 50 euro di spesa!</p>
                    </div>
                    
                </div> <!--fine grid values-->

            </div>
        </section> <!--fine sezione brand values-->

    </main>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="footer-content">
               <!-- Colonna 1: Brand -->
                <div class="footer-section">
                    <div class="footer-brand">
                        <div class="brand-name"><span lang="en">InfuseMe</span></div>
                        <div class="motto-brand">Taste Tradition</div>
                    </div>
                </div>

                <!-- Colonna 2: Contatti e Lavora con Noi-->
                <div class="footer-section">
                    <h3>Contatti</h3>
                    <address>
                        <div class="contact">
                            <strong>Centralino:</strong> +39 000 111 abcd 
                        </div>
                        <div class="contact">
                            <strong>Customer Care:</strong> +39 111 222 efgh
                        </div>
                        <div class="contact">
                            <strong>Assistenza Clienti:</strong> assistenza@infuseme.com
                        </div>
                    </address>

                    <h3>Lavora con Noi</h3>
                    <div class="work-item">
                        <strong>Carriere:</strong> hr@infuseme.com
                    </div>
                    <div class="work-item">
                        <strong>Collaborazioni:</strong> procurement@infuseme.com
                    </div>
                </div>

                <!-- Colonna 3: Orari Sedi -->
                <div class="footer-section">
                    <h3>Orari Ufficio</h3>
                    <div class="hours">
                        <strong>Lunedì - Venerdì:</strong> 9:00 - 18:00
                    </div>
                    <div class="hours">
                        <strong>Sabato:</strong> 9:00 - 13:00
                    </div>
                    <div class="hours">
                        <strong>Domenica:</strong> Chiuso
                    </div>

                    <h3>Le Nostre Sedi</h3>
                    <div class="location-item">Val d'Ossola (Piemonte)</div>
                    <div class="location-item">Biella</div>

                </div> <!--fine footer content-->


            <!-- Social Media Icons - solo icoe dei social-->
            <div class="footer-social">
                <h3>Seguici sui social</h3>
                <div class="social-icons">
                    <!-- Instagram -->
                    <span class="social-icon" aria-label="Instagram" title="Seguici su Instagram">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true">
                            <path d="m14.502,11.986c0,1.431-1.16,2.591-2.591,2.591s-2.59-1.16-2.59-2.591,1.16-2.591,2.59-2.591,2.591,1.16,2.591,2.591h0Zm0,0"/>
                            <path d="m12,0h0C5.373,0,0,5.373,0,12h0c0,6.627,5.373,12,12,12h0c6.627,0,12-5.373,12-12h0C24,5.373,18.627,0,12,0Zm7.637,15.19c-.037.827-.169,1.392-.361,1.886-.199.511-.465.945-.897,1.377-.432.432-.866.698-1.376.896-.494.192-1.06.323-1.887.361-.829.038-1.094.047-3.205.047s-2.375-.009-3.204-.047c-.827-.038-1.392-.169-1.887-.361-.511-.198-.944-.465-1.377-.896-.432-.432-.698-.866-.897-1.377-.192-.494-.323-1.059-.361-1.886-.038-.829-.047-1.094-.047-3.205s.009-2.375.047-3.204c.038-.827.169-1.392.361-1.887.199-.511.465-.944.897-1.376s.866-.698,1.377-.897c.494-.192,1.06-.323,1.887-.361.829-.038,1.094-.047,3.204-.047s2.376.009,3.205.047c.827.037,1.392.169,1.887.361.511.198.944.465,1.376.897.432.432.698.866.897,1.376.192.494.323,1.06.361,1.887.038.829.047,1.093.047,3.204s-.009,2.375-.047,3.205h0Zm-1.666-7.788c-.141-.363-.309-.622-.582-.894-.272-.272-.531-.441-.894-.582-.274-.106-.685-.233-1.443-.267-.82-.038-1.066-.045-3.141-.045s-2.321.008-3.141.045c-.757.034-1.169.161-1.443.267-.363.141-.622.309-.894.582-.272.272-.441.531-.582.894-.106.274-.233.685-.267,1.443-.038.819-.045,1.065-.045,3.141s.008,2.321.045,3.141c.035.757.161,1.169.267,1.443.141.363.309.622.582.894.272.272.531.44.894.581.274.107.685.233,1.443.268.819.038,1.065.045,3.141.045s2.322-.008,3.141-.045c.758-.035,1.169-.161,1.443-.268.363-.141.622-.309.894-.581s.441-.531.582-.894c.106-.274.233-.685.267-1.443.038-.82.046-1.066.046-3.141s-.008-2.321-.046-3.141c-.035-.758-.161-1.169-.267-1.443h0Zm-6.059,8.574c-2.204,0-3.991-1.787-3.991-3.991s1.787-3.991,3.991-3.991,3.991,1.787,3.991,3.991-1.787,3.991-3.991,3.991h0Zm4.149-7.207c-.515,0-.933-.417-.933-.932s.417-.933.933-.933.933.418.933.933-.418.932-.933.932h0Zm0,0"/>
                        </svg>
                    </span>

                    <!-- Facebook -->
                    <span class="social-icon" aria-label="Facebook" title="Seguici su Facebook">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M24,12.073c0,5.989-4.394,10.954-10.13,11.855v-8.363h2.789l0.531-3.46H13.87V9.86c0-0.947,0.464-1.869,1.95-1.869h1.509V5.045c0,0-1.37-0.234-2.679-0.234c-2.734,0-4.52,1.657-4.52,4.656v2.637H7.091v3.46h3.039v8.363C4.395,23.025,0,18.061,0,12.073c0-6.627,5.373-12,12-12S24,5.445,24,12.073z"/>
                        </svg>
                    </span>

                    <!-- TikTok -->
                    <span class="social-icon" aria-label="TikTok" title="Seguici su TikTok">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true">
                            <path d="m12,0C5.373,0,0,5.373,0,12s5.373,12,12,12,12-5.373,12-12S18.627,0,12,0h0Zm7.439,10.483c-1.52,0-2.93-.486-4.081-1.312v5.961c0,2.977-2.422,5.399-5.399,5.399-1.151,0-2.217-.363-3.094-.978-1.393-.978-2.305-2.594-2.305-4.421,0-2.977,2.422-5.399,5.399-5.399.247,0,.489.02.727.053v2.994c-.23-.072-.474-.114-.727-.114-1.36,0-2.466,1.106-2.466,2.466,0,.947.537,1.769,1.322,2.183.342.18.731.283,1.144.283,1.329,0,2.412-1.057,2.461-2.373l.005-11.756h2.933c0,.254.025.503.069.744.207,1.117.87,2.077,1.789,2.676.64.418,1.403.661,2.222.661v2.933Zm0,0"/>
                        </svg>
                    </span>

                    <!-- LinkedIn -->
                    <span class="social-icon" aria-label="LinkedIn" title="Seguici su LinkedIn">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true">
                            <path d="m20.65,18.172h-.174v-.353h.22c.114,0,.244.018.244.167,0,.171-.131.185-.29.185Z"/>
                            <path d="m20.639,17.366c-.473.008-.85.398-.843.871.008.473.398.851.871.843h0s.022,0,.022,0c.463-.01.83-.393.821-.856v-.014c-.008-.473-.398-.851-.871-.843h0Zm.31,1.378l-.285-.449-.004-.005h-.184v.454h-.149v-1.043h.398c.246,0,.367.095.367.294,0,.006,0,.012,0,.018-.001.147-.095.266-.289.266l.308.465h-.160Z"/>
                            <path d="m12,0h0C5.373,0,0,5.373,0,12h0c0,6.627,5.373,12,12,12h0c6.627,0,12-5.373,12-12h0C24,5.373,18.627,0,12,0Zm7.037,18.056c-.008.578-.483,1.042-1.062,1.034H5.76c-.577.006-1.051-.457-1.058-1.034V5.79c.007-.577.48-1.04,1.058-1.033h12.215c.578-.009,1.053.454,1.062,1.032v12.267Zm1.65,1.136c-.54.005-.982-.428-.987-.968-.005-.539.428-.981.968-.987h.019c.532.005.963.436.968.968.005.539-.428.981-.968.987Zm-6.32-9.232c-.823-.03-1.596.394-2.012,1.105h-.028v-.935h-2.039v6.84h2.124v-3.383c0-.893.169-1.756,1.276-1.756,1.09,0,1.104,1.021,1.104,1.814v3.326h2.124v-3.752c0-1.843-.396-3.258-2.549-3.258h0Zm-7.54,7.01h2.126v-6.84h-2.126v6.84Zm1.064-10.24c-.681,0-1.233.552-1.233,1.233,0,.681.552,1.232,1.233,1.232s1.233-.552,1.233-1.233c0-.681-.552-1.233-1.233-1.232Z"/>
                        </svg>
                    </span>

                    <!-- Pinterest -->
                    <span class="social-icon" aria-label="Pinterest" title="Seguici su Pinterest">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M12.01,0C5.388,0,0.02,5.368,0.02,11.99c0,5.082,3.158,9.424,7.618,11.171c-0.109-0.947-0.197-2.408,0.039-3.444c0.217-0.938,1.401-5.961,1.401-5.961s-0.355-0.72-0.355-1.776c0-1.668,0.967-2.911,2.171-2.911c1.026,0,1.52,0.77,1.52,1.688c0,1.026-0.651,2.566-0.997,3.997c-0.286,1.194,0.602,2.171,1.776,2.171c2.132,0,3.77-2.25,3.77-5.487c0-2.872-2.062-4.875-5.013-4.875c-3.414,0-5.418,2.556-5.418,5.201c0,1.026,0.395,2.132,0.888,2.734C7.52,14.615,7.53,14.724,7.5,14.842c-0.089,0.375-0.296,1.194-0.336,1.362c-0.049,0.217-0.178,0.266-0.405,0.158c-1.5-0.701-2.438-2.882-2.438-4.648c0-3.78,2.743-7.253,7.924-7.253c4.155,0,7.391,2.961,7.391,6.928c0,4.135-2.605,7.461-6.217,7.461c-1.214,0-2.359-0.632-2.743-1.382c0,0-0.602,2.289-0.75,2.852c-0.266,1.046-0.997,2.349-1.49,3.148C9.562,23.812,10.747,24,11.99,24,9.562,23.812,10.747,24,11.99,24c6.622,0,11.99-5.368,11.99-11.99C24,5.368,18.632,0,12.01,0z"/>
                        </svg>
                    </span>
                </div>
            </div>

            <div class="footer-bottom">
                <p>&copy; 2025 <span lang="en">InfuseMe</span>. Tutti i diritti riservati.</p>
            </div>

        </div> <!--fine class container-->
    </footer>

    <!-- Pulsante Torna Su (no title perchè c'è nel css)-->
   <button class="back-to-top" id="backToTop" aria-label="Torna all'inizio della pagina">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true">
            <path d="M18,15.5a1,1,0,0,1-.71-.29l-4.58-4.59a1,1,0,0,0-1.42,0L6.71,15.21a1,1,0,0,1-1.42-1.42L9.88,9.21a3.06,3.06,0,0,1,4.24,0l4.59,4.58a1,1,0,0,1,0,1.42A1,1,0,0,1,18,15.5Z"/>
        </svg>
    </button>

    <!--file js unico per tutti gli elementi -->
    <script src="javaScript/script.js"></script>

</body>
</html>