<?php
session_start();
require_once 'php/connessione.php';

// VERIFICA LOGIN: se l'utente non è loggato, lo rimanda al login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// LOGICA CHECKOUT
$ordine_completato = false;
$errore_ordine = "";
$id_ordine_creato = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['conferma_ordine'])) {
    if (!empty($_SESSION['carrello'])) {
        $id_utente = $_SESSION['user_id'];
        $indirizzo_spedizione = trim($_POST['indirizzo_spedizione']);
        $totale_ordine = $_POST['totale_calcolato'];
        
        if (empty($indirizzo_spedizione)) {
            $errore_ordine = "L'indirizzo di spedizione è obbligatorio.";
        } else {
            try {
                // 1. Inserimento Ordine
                $stmt = $pdo->prepare("INSERT INTO ordine (id_utente, indirizzo_spedizione, totale, stato_ord, data_ordine) VALUES (?, ?, ?, 'in_attesa', NOW())");
                
                // Con PDO passiamo i parametri direttamente nell'execute
                if ($stmt->execute([$id_utente, $indirizzo_spedizione, $totale_ordine])) {
                    
                    // Recupero ID ordine
                    $id_ordine_creato = $pdo->lastInsertId();
                    
                    // 2. Inserimento Dettagli
                    foreach ($_SESSION['carrello'] as $item) {
                        $id_prodotto_finale = null;
                        $id_custom_finale = null;

                        // Gestione Custom Blend
                        if ($item['tipo'] === 'custom') {
                            $nome_blend = $item['nome'];
                            $id_base = $item['id_base'];
                            $prezzo = $item['prezzo'];
                            $num_ingredienti = isset($item['ids_ingredienti']) ? count($item['ids_ingredienti']) : 0;
                            
                            $nomi_ing = is_array($item['ingredienti']) ? implode(", ", $item['ingredienti']) : $item['ingredienti'];
                            $descrizione = "Blend creato dall'utente su base " . $item['base'] . ". Ingredienti: " . $nomi_ing;

                            // Insert in prodotto_custom
                            $stmt_custom = $pdo->prepare("INSERT INTO prodotto_custom (nome_blend, descrizione, num_ingredienti, prezzo, id_base) VALUES (?, ?, ?, ?, ?)");
                            $stmt_custom->execute([$nome_blend, $descrizione, $num_ingredienti, $prezzo, $id_base]);
                            
                            $id_custom_finale = $pdo->lastInsertId();

                            // Insert ingredienti relazione
                            if (!empty($item['ids_ingredienti'])) {
                                $stmt_ing = $pdo->prepare("INSERT INTO custom_ingrediente (id_custom, id_ingrediente) VALUES (?, ?)");
                                foreach ($item['ids_ingredienti'] as $id_ing) {
                                    $stmt_ing->execute([$id_custom_finale, $id_ing]);
                                }
                            }
                            
                        } else {
                            // Prodotto Standard
                            $id_prodotto_finale = $item['id'];
                        }

                        // Inserimento riga dettaglio
                        $stmt_det = $pdo->prepare("INSERT INTO dettaglio_ordine (id_ordine, id_prodotto, id_custom, quantita, prezzo_unit) VALUES (?, ?, ?, ?, ?)");
                        // Nota: passiamo NULL se l'ID non c'è
                        $stmt_det->execute([$id_ordine_creato, $id_prodotto_finale, $id_custom_finale, $item['quantita'], $item['prezzo']]);
                    }
                    
                    unset($_SESSION['carrello']);
                    $ordine_completato = true;
                } else {
                    $errore_ordine = "Errore durante il salvataggio dell'ordine.";
                }
            } catch (PDOException $e) {
                $errore_ordine = "Errore tecnico: Impossibile salvare l'ordine.";
                error_log("Errore PDO: " . $e->getMessage());
            }
        }
    }
}

// RECUPERO INDIRIZZO UTENTE
$indirizzo_precompilato = "";
if (!$ordine_completato && isset($_SESSION['user_id'])) {
    $stmt = $pdo->prepare("SELECT indirizzo, citta, cap, paese FROM utente WHERE id_utente = ?");
    $stmt->execute([$_SESSION['user_id']]);
    
    // Fetch con PDO
    if ($r = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $parti = [];
        if (!empty($r['indirizzo'])) $parti[] = $r['indirizzo'];
        if (!empty($r['citta'])) $parti[] = $r['citta'];
        if (!empty($r['cap'])) $parti[] = $r['cap'];
        $indirizzo_precompilato = implode(", ", $parti);
    }
}
?>


<!DOCTYPE html>
<html lang="it" xml:lang="it" xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0" />
    <title>Carrello - InfuseMe</title>
    <meta name="description" content="Gestisci il tuo carrello e completa l'ordine dei tuoi infusi preferiti." />
    <meta name="keywords" content="tè, infusi, tisane, carrello, biologico, artigianale, prodotto, catalogo, blend, acquisto" />
    <link rel="stylesheet" href="style.css" type="text/css" />
</head>
<body>
    <a href="#main-content" class="skip-link">Salta al contenuto principale</a>

    <?php include 'navbar.php'; ?>

    <main id="main-content" role="main">
        <?php if ($ordine_completato): ?>
            <section class="cart-message-container admin-card" role="alert">
                <h1>Grazie! L'ordine è stato completato.</h1>
                <p>Abbiamo ricevuto la tua richiesta. Stiamo preparando i tuoi infusi.</p>
                <div class="cart-actions">
                    <a href="home.php" class="bottone-primario">Torna alla Home</a>
                    <a href="dettaglioOrdine.php?id=<?php echo $id_ordine_creato; ?>" class="bottone-primario">Visualizza l'ordine</a>
                </div>
            </section>

        <?php elseif (empty($_SESSION['carrello'])): ?>
            <section class="cart-message-container admin-card">
                <h1>Il tuo carrello è vuoto</h1>
                <p>Non hai ancora aggiunto infusi al tuo carrello.</p>
                <div class="cart-actions">
                    <a href="catalogo.php" class="bottone-primario">Vai al Catalogo</a>
                </div>
            </section>

        <?php else: ?>
            <h1>Il tuo Carrello</h1>
                
            <?php if ($errore_ordine): ?>
                <div class="errorSuggestion" role="alert">
                    <?php echo htmlspecialchars($errore_ordine); ?>
                </div>
            <?php endif; ?>

            <div class="cart-layout">
                <div class="cart-list-container admin-card"> <!--riutilizzo stili da altre pagine-->
                    <ul class="cart-list" aria-label="Elenco prodotti nel carrello">
                        <?php 
                        $totale = 0;
                        foreach ($_SESSION['carrello'] as $key => $item): 
                            $subtotale = $item['prezzo'] * $item['quantita'];
                            $totale += $subtotale;
                        ?>
                        <li class="cart-item">
                            <div class="cart-item-info">
                                <h2><?php echo htmlspecialchars($item['nome']); ?></h2>
                                    
                                <?php if ($item['tipo'] == 'custom'): ?>
                                    <div class="product-grams">
                                        <span>Base: <?php echo htmlspecialchars($item['base']); ?></span>
                                        <span>Ingredienti: <?php echo htmlspecialchars(is_array($item['ingredienti']) ? implode(", ", $item['ingredienti']) : $item['ingredienti']); ?></span>
                                    </div>
                                <?php else: ?>
                                    <span class="product-grams"><?php echo $item['grammi']; ?>g - Confezione classica</span>
                                <?php endif; ?>
                            </div>

                            <div class="quantity-selector">
                                <form action="php/gestioneCarrello.php" method="POST">
                                <input type="hidden" name="azione" value="aggiorna">
                                <input type="hidden" name="chiave_carrello" value="<?php echo $key; ?>">
                                
                                <label for="qty-<?php echo $key; ?>" class="sr-only">Quantità</label>
                                
                                <div class="quantity-controls">
                                    <button type="submit" name="nuova_quantita" value="<?php echo $item['quantita'] - 1; ?>" class="quantity-btn" aria-label="Diminuisci">-</button>
                                    
                                    <input type="number" id="qty-<?php echo $key; ?>" value="<?php echo $item['quantita']; ?>" readonly>
                                    
                                    <button type="submit" name="nuova_quantita" value="<?php echo $item['quantita'] + 1; ?>" class="quantity-btn" aria-label="Aumenta">+</button>
                                </div>
                                </form>
                            </div>

                            <div class="product-price cart-price-fix">
                                € <?php echo number_format($subtotale, 2); ?>
                            </div>

                            <div class="cart-item-remove">
                                <form action="php/gestioneCarrello.php" method="POST">
                                    <input type="hidden" name="azione" value="rimuovi">
                                    <input type="hidden" name="chiave_carrello" value="<?php echo $key; ?>">
                                    <button type="submit" class="bottone-primario" aria-label="Rimuovi <?php echo htmlspecialchars($item['nome']); ?>">Rimuovi</button>
                                </form>
                            </div>
                        </li>
                        <?php endforeach; ?>
                            
                        <li class="cart-total-row">
                            <span>Totale Ordine:</span>
                            <strong class="total-price">€ <?php echo number_format($totale, 2); ?></strong>
                        </li>
                    </ul>
                </div>

                <section class="cart-summary admin-card" aria-labelledby="titolo-spedizione">
                    <h2>Dati di Spedizione</h2>
                        
                    <form action="carrello.php" method="POST" class="form-checkout">
                        <input type="hidden" name="totale_calcolato" value="<?php echo $totale; ?>">
                            
                        <div class="form-group">
                            <label for="indirizzo_spedizione">Indirizzo di consegna:</label>
                            <textarea id="indirizzo_spedizione" name="indirizzo_spedizione" rows="3" required aria-required="true"><?php echo htmlspecialchars($indirizzo_precompilato); ?></textarea>
                        </div>

                        <div class="checkout">
                            <button type="submit" name="conferma_ordine" class="bottone-primario">Conferma Ordine</button>
                        </div>
                    </form>
                </section>

            </div>
        <?php endif; ?>
    </main>

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

   <button class="back-to-top" id="backToTop" aria-label="Torna all'inizio della pagina">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true">
            <path d="M18,15.5a1,1,0,0,1-.71-.29l-4.58-4.59a1,1,0,0,0-1.42,0L6.71,15.21a1,1,0,0,1-1.42-1.42L9.88,9.21a3.06,3.06,0,0,1,4.24,0l4.59,4.58a1,1,0,0,1,0,1.42A1,1,0,0,1,18,15.5Z"/>
        </svg>
    </button>

    <script src="javaScript/script.js"></script>
</body>
</html>