<?php
session_start();
require_once 'php/connessione.php';

// VERIFICA LOGIN
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
        $sottototale = floatval($_POST['sottototale_calcolato']);
        $spese_spedizione = 4.99;
        $totale_ordine = $sottototale + $spese_spedizione;
        
        $omaggio = ($totale_ordine >= 50) ? 1 : 0;
        $descrizione_omaggio = ($omaggio === 1) ? "Infuso Alpino - Edizione 50° Anniversario" : null;

        if (empty($indirizzo_spedizione)) {
            $errore_ordine = "L'indirizzo di spedizione è obbligatorio.";
        } else {
            try {
                $sql = "INSERT INTO ordine (id_utente, indirizzo_spedizione, sottotota, spese_spedizione, totale, stato_ord, data_ordine, omaggio, descrizione_omaggio) 
                VALUES (?, ?, ?, ?, ?, 'in_attesa', NOW(), ?, ?)";
                $stmt = $pdo->prepare($sql);

                if ($stmt->execute([$id_utente, $indirizzo_spedizione, $sottototale, $spese_spedizione, $totale_ordine, $omaggio, $descrizione_omaggio])) {
                    $id_ordine_creato = $pdo->lastInsertId();
                    
                    foreach ($_SESSION['carrello'] as $item) {
                        $id_prodotto_finale = null;
                        $id_custom_finale = null;

                        if ($item['tipo'] === 'custom') {
                            $nome_blend = $item['nome'];
                            $id_base = $item['id_base'];
                            $prezzo = $item['prezzo'];
                            $num_ingredienti = isset($item['ids_ingredienti']) ? count($item['ids_ingredienti']) : 0;
                            
                            $nomi_ing = is_array($item['ingredienti']) ? implode(", ", $item['ingredienti']) : $item['ingredienti'];
                            $descrizione = "Blend creato dall'utente su base " . $item['base'] . ". Ingredienti: " . $nomi_ing;

                            $stmt_custom = $pdo->prepare("INSERT INTO prodotto_custom (nome_blend, descrizione, num_ingredienti, prezzo, id_base) VALUES (?, ?, ?, ?, ?)");
                            $stmt_custom->execute([$nome_blend, $descrizione, $num_ingredienti, $prezzo, $id_base]);
                            
                            $id_custom_finale = $pdo->lastInsertId();

                            if (!empty($item['ids_ingredienti'])) {
                                $stmt_ing = $pdo->prepare("INSERT INTO custom_ingrediente (id_custom, id_ingrediente) VALUES (?, ?)");
                                foreach ($item['ids_ingredienti'] as $id_ing) {
                                    $stmt_ing->execute([$id_custom_finale, $id_ing]);
                                }
                            }
                            
                        } else {
                            $id_prodotto_finale = $item['id'];
                        }
                        
                        $stmt_det = $pdo->prepare("INSERT INTO dettaglio_ordine (id_ordine, id_prodotto, id_custom, quantita, prezzo_unit) VALUES (?, ?, ?, ?, ?)");
                        $stmt_det->execute([$id_ordine_creato, $id_prodotto_finale, $id_custom_finale, $item['quantita'], $item['prezzo']]);
                    }
                    
                    unset($_SESSION['carrello']);
                    $ordine_completato = true;
                } else {
                    $errore_ordine = "Errore durante il salvataggio dell'ordine.";
                }
            } catch (PDOException $e) {
                $errore_ordine = "Errore nel Database: " . $e->getMessage();
                error_log("Errore Ordine: " . $e->getMessage());
            }
        }
    }
}

// RECUPERO INDIRIZZO UTENTE
$indirizzo_precompilato = "";
if (!$ordine_completato && isset($_SESSION['user_id'])) {
    $stmt = $pdo->prepare("SELECT indirizzo, citta, cap, paese FROM utente WHERE id_utente = ?");
    $stmt->execute([$_SESSION['user_id']]);
    
    if ($r = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $parti = [];
        if (!empty($r['indirizzo'])) $parti[] = $r['indirizzo'];
        if (!empty($r['citta'])) $parti[] = $r['citta'];
        if (!empty($r['cap'])) $parti[] = $r['cap'];
        $indirizzo_precompilato = implode(", ", $parti);
    }
}

// CALCOLO CARRELLO (se non vuoto)
$carrello_html = '';
$sottototale = 0;
$spedizione = 4.99;

if (!empty($_SESSION['carrello'])) {
    foreach ($_SESSION['carrello'] as $key => $item) {
        $importo_riga = $item['prezzo'] * $item['quantita'];
        $sottototale += $importo_riga;
        
        // Genera HTML per ogni item
        $carrello_html .= '<li class="cart-item">';
        $carrello_html .= '<div class="cart-item-info">';
        $carrello_html .= '<h2>' . htmlspecialchars($item['nome']) . '</h2>';
        
        if ($item['tipo'] == 'custom') {
            $ingredienti_str = is_array($item['ingredienti']) ? implode(", ", $item['ingredienti']) : $item['ingredienti'];
            $carrello_html .= '<div class="product-grams">';
            $carrello_html .= '<span>Base: ' . htmlspecialchars($item['base']) . '</span>';
            $carrello_html .= '<span>Ingredienti: ' . htmlspecialchars($ingredienti_str) . '</span>';
            $carrello_html .= '</div>';
        } else {
            $carrello_html .= '<span class="product-grams">' . $item['grammi'] . 'g - Confezione classica</span>';
        }
        
        $carrello_html .= '</div>';
        
        // Quantity selector
        $carrello_html .= '<div class="quantity-selector">';
        $carrello_html .= '<form action="php/gestioneCarrello.php" method="POST">';
        $carrello_html .= '<input type="hidden" name="azione" value="aggiorna">';
        $carrello_html .= '<input type="hidden" name="chiave_carrello" value="' . $key . '">';
        $carrello_html .= '<label for="qty-' . $key . '" class="sr-only">Quantità</label>';
        $carrello_html .= '<div class="quantity-controls">';
        $carrello_html .= '<button type="submit" name="nuova_quantita" value="' . ($item['quantita'] - 1) . '" class="quantity-btn" aria-label="Diminuisci">-</button>';
        $carrello_html .= '<input type="number" id="qty-' . $key . '" value="' . $item['quantita'] . '" readonly>';
        $carrello_html .= '<button type="submit" name="nuova_quantita" value="' . ($item['quantita'] + 1) . '" class="quantity-btn" aria-label="Aumenta">+</button>';
        $carrello_html .= '</div></form></div>';
        
        $carrello_html .= '<div class="product-price cart-price-fix">€ ' . number_format($importo_riga, 2) . '</div>';
        
        // Remove button
        $carrello_html .= '<div class="cart-item-remove">';
        $carrello_html .= '<form action="php/gestioneCarrello.php" method="POST">';
        $carrello_html .= '<input type="hidden" name="azione" value="rimuovi">';
        $carrello_html .= '<input type="hidden" name="chiave_carrello" value="' . $key . '">';
        $carrello_html .= '<button type="submit" class="bottone-primario" aria-label="Rimuovi ' . htmlspecialchars($item['nome']) . '">Rimuovi</button>';
        $carrello_html .= '</form></div>';
        $carrello_html .= '</li>';
    }
    
    // Omaggio se >= 50
    if ($sottototale >= 50) {
        $carrello_html .= '<li class="cart-item omaggio-item">';
        $carrello_html .= '<div class="cart-item-info">';
        $carrello_html .= '<h2>Prodotto in Omaggio 🎁</h2>';
        $carrello_html .= '<h3>Infuso Alpino - Edizione <abbr title="cinquantesimo">50°</abbr> Anniversario</h3>';
        $carrello_html .= '</div>';
        $carrello_html .= '<div class="product-grams">1 pezzo</div>';
        $carrello_html .= '<div class="product-price cart-price-fix"> Gratis </div>';
        $carrello_html .= '<div class="cart-item-remove"></div>';
        $carrello_html .= '</li>';
    }
    
    $totale_finale = $sottototale + $spedizione;
    
    // Righe totali
    $carrello_html .= '<li class="cart-total-row subtotal">';
    $carrello_html .= '<span>Sottototale:</span>';
    $carrello_html .= '<span class="total-price">€ ' . number_format($sottototale, 2) . '</span>';
    $carrello_html .= '</li>';
    
    $carrello_html .= '<li class="cart-total-row subtotal">';
    $carrello_html .= '<span>Spedizione standard:</span>';
    $carrello_html .= '<span class="total-price">€ ' . number_format($spedizione, 2) . '</span>';
    $carrello_html .= '</li>';
    
    $carrello_html .= '<li class="cart-total-row">';
    $carrello_html .= '<span>Totale Ordine:</span>';
    $carrello_html .= '<strong class="total-price">€ ' . number_format($totale_finale, 2) . '</strong>';
    $carrello_html .= '</li>';
}

// Carica navbar
ob_start();
include 'navbar.php';
$navbar_html = ob_get_clean();

// Carica template
$templatePath = __DIR__ . '/html/carrello.html';

if (file_exists($templatePath)) {
    $template = file_get_contents($templatePath);
    
    // Sostituzioni
    $template = str_replace('[NAVBAR]', $navbar_html, $template);
    $template = str_replace('[ORDINE_COMPLETATO]', $ordine_completato ? '1' : '0', $template);
    $template = str_replace('[ID_ORDINE]', $id_ordine_creato ?? '', $template);
    $template = str_replace('[CARRELLO_VUOTO]', empty($_SESSION['carrello']) ? '1' : '0', $template);
    $template = str_replace('[ERRORE_ORDINE]', htmlspecialchars($errore_ordine), $template);
    $template = str_replace('[CARRELLO_ITEMS]', $carrello_html, $template);
    $template = str_replace('[SOTTOTOTALE]', number_format($sottototale, 2), $template);
    $template = str_replace('[INDIRIZZO]', htmlspecialchars($indirizzo_precompilato), $template);
    $template = str_replace('[INDIRIZZO_VUOTO]', empty($indirizzo_precompilato) ? '1' : '0', $template);
    
    echo $template;
} else {
    die("Errore: Template non trovato.");
}
?>