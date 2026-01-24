<?php
session_start();
require_once 'php/connessione.php';

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
        
        $indirizzo_spedizione = trim($_POST['indirizzo_spedizione'] ?? '');
        $sottototale = floatval($_POST['sottototale_calcolato'] ?? 0);
        $spese_spedizione = 4.99;
        $totale_ordine = $sottototale + $spese_spedizione;
        
        $omaggio = ($totale_ordine >= 50) ? 1 : 0;
        $descrizione_omaggio = ($omaggio === 1) ? "Infuso Alpino - Edizione 50° Anniversario" : null;

        if (empty($indirizzo_spedizione)) {
            $errore_ordine = "L'indirizzo di spedizione è obbligatorio.";
        } else {
            try {
                $pdo->beginTransaction();

                // INSERIMENTO ORDINE
                $sql = "INSERT INTO ordine (id_utente, indirizzo_spedizione, sottototale, spese_spedizione, totale, stato_ord, data_ordine, omaggio, descrizione_omaggio) 
                        VALUES (?, ?, ?, ?, ?, 'in_attesa', NOW(), ?, ?)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$id_utente, $indirizzo_spedizione, $sottototale, $spese_spedizione, $totale_ordine, $omaggio, $descrizione_omaggio]);
                $id_ordine_creato = $pdo->lastInsertId();

                // INSERIMENTO DETTAGLI
                $sql_dett = "INSERT INTO dettaglio_ordine (id_ordine, id_prodotto, id_custom, quantita, prezzo_unit) VALUES (?, ?, ?, ?, ?)";
                $stmt_dett = $pdo->prepare($sql_dett);

                foreach ($_SESSION['carrello'] as $item) {
                    $id_prodotto_finale = null;
                    $id_custom_finale = null;

                    // Gestione BLEND CUSTOM
                    if ($item['tipo'] === 'custom') {
                        $nome_blend = $item['nome'];
                        $id_base = $item['id_base'] ?? 1;
                        $prezzo = $item['prezzo'];
                        
                        $nomi_ing = is_array($item['ingredienti']) ? implode(", ", $item['ingredienti']) : $item['ingredienti'];
                        $descrizione = "Blend creato dall'utente. Base: " . ($item['base'] ?? 'Custom') . ". Ingredienti: " . $nomi_ing;
                        $num_ingredienti = substr_count($nomi_ing, ',') + 1;

                        $stmt_custom = $pdo->prepare("INSERT INTO prodotto_custom (nome_blend, descrizione, num_ingredienti, prezzo, id_base) VALUES (?, ?, ?, ?, ?)");
                        $stmt_custom->execute([$nome_blend, $descrizione, $num_ingredienti, $prezzo, $id_base]);
                        $id_custom_finale = $pdo->lastInsertId();
                    } else {
                        // PRODOTTO STANDARD
                        $id_prodotto_finale = $item['id'];
                    }
                    
                    $stmt_dett->execute([
                        $id_ordine_creato, 
                        $id_prodotto_finale, 
                        $id_custom_finale, 
                        $item['quantita'], 
                        $item['prezzo']
                    ]);
                }

                $pdo->commit();
                $_SESSION['carrello'] = [];
                $ordine_completato = true;

            } catch (PDOException $e) {
                $pdo->rollBack();
                $errore_ordine = "Errore nel salvataggio ordine: " . $e->getMessage();
            }
        }
    }
}

// RECUPERO INDIRIZZO
$indirizzo_precompilato = "";
if (!$ordine_completato && isset($_SESSION['user_id'])) {
    $stmt = $pdo->prepare("SELECT indirizzo, citta, cap FROM utente WHERE id_utente = ?");
    $stmt->execute([$_SESSION['user_id']]);
    if ($r = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $parti = [];
        if (!empty($r['indirizzo'])) $parti[] = $r['indirizzo'];
        if (!empty($r['citta'])) $parti[] = $r['citta'];
        if (!empty($r['cap'])) $parti[] = $r['cap'];
        $indirizzo_precompilato = implode(", ", $parti);
    }
}

// GENERAZIONE HTML DINAMICO
$html_output = '';

// CASO 1: SUCCESSO
if ($ordine_completato) {
    $html_output = <<<HTML
    <section class="cart-message-container user-card" role="alert">
        <h1>Grazie! L'ordine è stato completato.</h1>
        <h2>Abbiamo ricevuto la tua richiesta. Numero ordine: <strong>#{$id_ordine_creato}</strong></h2>
        <div class="cart-actions">
            <a href="index.php" class="bottone-primario">Torna alla Home</a>
            <a href="paginaUtente.php" class="bottone-primario">I miei ordini</a>
        </div>
    </section>
HTML;

// CASO 2: VUOTO
} elseif (empty($_SESSION['carrello'])) {
    $html_output = <<<HTML
    <section class="cart-message-container user-card">
        <h1>Il tuo carrello è vuoto</h1>
        <h2>Non hai ancora aggiunto prodotti.</h2>
        <div class="cart-actions">
            <a href="catalogo.php" class="bottone-primario">Vai al Catalogo</a>
        </div>
    </section>
HTML;

// CASO 3: PIENO
} else {
    $items_html = '';
    $sottototale = 0;
    
    foreach ($_SESSION['carrello'] as $chiave => $item) {
        $prezzo = floatval($item['prezzo']);
        $qty = intval($item['quantita']);
        $importo_riga = $prezzo * $qty; //totale per questa riga
        $sottototale += $importo_riga; //totale generale accumulato
        $prezzoRigaFmt = number_format($importo_riga, 2); 
        $nome = htmlspecialchars($item['nome']);
        
        // Dettagli custom o standard
        $dettagli = '';
        if ($item['tipo'] === 'custom') {
            $base = htmlspecialchars($item['base'] ?? 'Base');
            $ingText = htmlspecialchars(is_array($item['ingredienti']) ? implode(", ", $item['ingredienti']) : $item['ingredienti']);
            $dettagli = <<<HTML
            <div class="product-grams">
                <span>Base: {$base}</span><br>
                <span>Ingredienti: {$ingText}</span>
            </div>
HTML;
        } else {
            $grammi = htmlspecialchars($item['grammi'] ?? '50');
            $dettagli = "<span class=\"product-grams\">{$grammi}g - Confezione classica</span>";
        }

        $qtyMinus = $qty - 1;
        $qtyPlus = $qty + 1;

        $items_html .= <<<HTML
        <li class="cart-item">
            <div class="cart-item-info">
                <h2>{$nome}</h2>
                {$dettagli}
            </div>

            <div class="quantity-selector">
                <form action="php/gestioneCarrello.php" method="POST">
                    <input type="hidden" name="azione" value="aggiorna">
                    <input type="hidden" name="chiave_carrello" value="{$chiave}">
                    <label for="qty-{$chiave}" class="sr-only">Quantità</label>
                    
                    <div class="quantity-controls">
                        <button type="submit" name="nuova_quantita" value="{$qtyMinus}" class="quantity-btn" aria-label="Diminuisci">-</button>
                        <input type="number" id="qty-{$chiave}" value="{$qty}" readonly>
                        <button type="submit" name="nuova_quantita" value="{$qtyPlus}" class="quantity-btn" aria-label="Aumenta">+</button>
                    </div>
                </form>
            </div>

            <div class="product-price cart-price-fix">
                € {$prezzoRigaFmt}
            </div>

            <div class="cart-item-remove">
                <form action="php/gestioneCarrello.php" method="POST">
                    <input type="hidden" name="azione" value="rimuovi">
                    <input type="hidden" name="chiave_carrello" value="{$chiave}">
                    <button type="submit" class="bottone-primario" aria-label="Rimuovi {$nome}">Rimuovi</button>
                </form>
            </div>
        </li>
HTML;
    }

    // Omaggio
    if ($sottototale >= 50) {
        $items_html .= <<<HTML
        <li class="cart-item omaggio-item">
            <div class="cart-item-info">
                <h2>Prodotto in Omaggio 🎁</h2>
                <h3>Infuso Alpino - Edizione <abbr title="cinquantesimo">50°</abbr> Anniversario</h3>
            </div>
            <div class="product-grams">1 pezzo</div>
            <div class="product-price cart-price-fix"> Gratis </div>
            <div class="cart-item-remove"></div> 
        </li>
HTML;
    }

    $spedizione = 4.99;
    $totale_finale = $sottototale + $spedizione;
    
    $sotFmt = number_format($sottototale, 2);
    $speFmt = number_format($spedizione, 2);
    $totFmt = number_format($totale_finale, 2);

    $items_html .= <<<HTML
    <li class="cart-total-row subtotal">
        <span>Sottototale:</span>
        <span class="total-price">€ {$sotFmt}</span>
    </li>
    <li class="cart-total-row subtotal">
        <span>Spedizione standard:</span>
        <span class="total-price">€ {$speFmt}</span>
    </li>
    <li class="cart-total-row">
        <span>Totale Ordine:</span>
        <strong class="total-price">€ {$totFmt}</strong>
    </li>
HTML;

    // Indirizzo
    $indirizzo_block = '';
    $disabled = '';
    
    if (!empty($indirizzo_precompilato)) {
        $addrSafe = htmlspecialchars($indirizzo_precompilato);
        $indirizzo_block = <<<HTML
        <div class="indirizzo-container">
            <span class="indirizzo_spedizione">Indirizzo di consegna:</span>
            <p>{$addrSafe}</p>
            <p class="indirizzo_spedizione">L'indirizzo non è corretto?
                <a href="paginaUtente.php">Modificalo nel profilo</a>
            </p>
        </div>
HTML;
    } else {
        $disabled = 'disabled';
        $indirizzo_block = <<<HTML
        <div class="indirizzo-container">
            <p class="errorSuggestion">Non hai impostato un indirizzo nel tuo profilo.</p>
            <a href="paginaUtente.php" class="bottone-primario">Vai al Profilo per inserirlo</a>
        </div>
HTML;
    }
    
    // Gestione Errore
    $error_block = '';
    if ($errore_ordine) {
        $msg = htmlspecialchars($errore_ordine);
        $error_block = "<div class=\"errorSuggestion\" role=\"alert\">{$msg}</div>";
    }

    $addrValue = htmlspecialchars($indirizzo_precompilato);

    // Layout Finale
    $html_output = <<<HTML
    <h1>Il tuo Carrello</h1>
    {$error_block}

    <div class="cart-layout">
        <div class="cart-list-container user-card">
            <ul class="cart-list" aria-label="Elenco prodotti nel carrello">
                {$items_html}
            </ul>
        </div>

        <section class="cart-summary user-card" aria-labelledby="titolo-spedizione">
            <form action="carrello.php" method="POST" class="form-checkout">
                <input type="hidden" name="sottototale_calcolato" value="{$sottototale}">
                <input type="hidden" name="indirizzo_spedizione" value="{$addrValue}">
                
                {$indirizzo_block}
                
                <div class="checkout">
                    <button type="submit" name="conferma_ordine" class="bottone-primario" {$disabled}>
                        Conferma Ordine
                    </button>
                </div>
            </form>
        </section>
    </div>
HTML;
}

// 5. STAMPA TEMPLATE
require_once 'php/navbar.php';
$templatePath = 'html/carrello.html';

if (file_exists($templatePath)) {
    $template = file_get_contents($templatePath);
    $template = str_replace('[navbar]', $navbarBlock, $template);
    $template = str_replace('[CONTENUTO_CARRELLO]', $html_output, $template);
    echo $template;
} else {
    die("Errore: Template carrello.html non trovato.");
}
?>