<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'php/connessione.php';

// Verifica ID prodotto
$id_prodotto = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id_prodotto <= 0) {
    header('Location: catalogo.php');
    exit();
}

try {
    // Query prodotto
    $sql_prodotto = "SELECT p.*, 
                     b.nome as nome_base, 
                     b.img_path as img_base,
                     b.temperatura_infusione, 
                     b.tempo_infusione,
                     GROUP_CONCAT(DISTINCT CONCAT(i.nome, '|||', COALESCE(i.img_path, '')) SEPARATOR '###') as ingredienti_con_img
                     FROM prodotto p
                     LEFT JOIN base b ON p.id_base = b.id_base
                     LEFT JOIN prodotto_ingrediente pi ON p.id_prodotto = pi.id_prodotto
                     LEFT JOIN ingrediente i ON pi.id_ingrediente = i.id_ingrediente
                     WHERE p.id_prodotto = :id_prodotto
                     GROUP BY p.id_prodotto";
    
    $stmt_prodotto = $pdo->prepare($sql_prodotto);
    $stmt_prodotto->execute([':id_prodotto' => $id_prodotto]);
    $prodotto = $stmt_prodotto->fetch(PDO::FETCH_ASSOC);
    
    if (!$prodotto) {
        header('Location: catalogo.php');
        exit();
    }
    
    // Processa ingredienti
    $ingredienti_con_img = [];
    $lista_ingredienti_nomi = [];
    
    if (!empty($prodotto['ingredienti_con_img'])) {
        $ingredienti_array = explode('###', $prodotto['ingredienti_con_img']);
        foreach ($ingredienti_array as $ingrediente) {
            $parts = explode('|||', $ingrediente);
            if (count($parts) >= 2 && !empty(trim($parts[0]))) {
                $nome_ing = trim($parts[0]);
                $img_ing = trim($parts[1]);
                $ingredienti_con_img[] = [
                    'nome' => $nome_ing,
                    'img_path' => !empty($img_ing) ? $img_ing : 'images/ingredienti/default-ingrediente.webp'
                ];
                $lista_ingredienti_nomi[] = $nome_ing;
            }
        }
    }
    
    // Escape dati
    $nome = htmlspecialchars($prodotto['nome'], ENT_QUOTES, 'UTF-8');
    $descrizione = htmlspecialchars($prodotto['descrizione'], ENT_QUOTES, 'UTF-8');
    $descrizione_formattata = nl2br(preg_replace('/\.\s+/', ".\n", $descrizione));
    $img_path = htmlspecialchars($prodotto['img_path'], ENT_QUOTES, 'UTF-8');
    $prezzo = number_format($prodotto['prezzo'], 2, ',', '.');
    $grammi = htmlspecialchars($prodotto['grammi'], ENT_QUOTES, 'UTF-8');
    $categoria = htmlspecialchars($prodotto['categoria'], ENT_QUOTES, 'UTF-8');
    $categoria_display = ucfirst(str_replace('_', ' ', $categoria));
    $base_nome = htmlspecialchars($prodotto['nome_base'] ?? 'Non specificata', ENT_QUOTES, 'UTF-8');
    $temperatura = htmlspecialchars($prodotto['temperatura_infusione'] ?? 'Non specificata', ENT_QUOTES, 'UTF-8');
    $tempo = htmlspecialchars($prodotto['tempo_infusione'] ?? 'Non specificato', ENT_QUOTES, 'UTF-8');
    $disponibilita = $prodotto['disponibilita'];
    
    // Genera HTML ingredienti
    $ingredienti_html = '';
    if (!empty($ingredienti_con_img)) {
        $ingredienti_html .= '<div class="altri-ingredienti">';
        $ingredienti_html .= '<h3>Ingredienti aggiuntivi</h3>';
        $ingredienti_html .= '<div class="ingredienti-grid">';
        
        foreach ($ingredienti_con_img as $ingrediente) {
            $nome_ing_esc = htmlspecialchars($ingrediente['nome'], ENT_QUOTES, 'UTF-8');
            $img_ing_esc = htmlspecialchars($ingrediente['img_path'], ENT_QUOTES, 'UTF-8');
            
            $ingredienti_html .= '<div class="ingrediente-card">';
            $ingredienti_html .= '<img src="' . $img_ing_esc . '" alt="' . $nome_ing_esc . '" onerror="this.src=\'images/ingredienti/default-ingrediente.webp\'">';
            $ingredienti_html .= '<p class="ingrediente-nome">' . $nome_ing_esc . '</p>';
            $ingredienti_html .= '</div>';
        }
        
        $ingredienti_html .= '</div></div>';
    }
    
    // Genera badge disponibilità
    $badge_disponibilita = '';
    if ($disponibilita <= 10 && $disponibilita > 0) {
        $badge_disponibilita = '<div class="availability-badge">Ultimi ' . $disponibilita . ' disponibili!</div>';
    } elseif ($disponibilita == 0) {
        $badge_disponibilita = '<div class="availability-badge out-of-stock">Esaurito</div>';
    }
    
    // Genera form carrello
    $form_carrello_html = '';
    if ($disponibilita > 0) {
        $form_carrello_html = '
        <form action="php/gestioneCarrello.php" method="POST">
            <input type="hidden" name="azione" value="aggiungi">
            <input type="hidden" name="tipo" value="standard">
            <input type="hidden" name="id_prodotto" value="' . $id_prodotto . '">

            <div class="quantity-selector">
                <label for="quantita">Quantità:</label>
                <div class="quantity-controls">
                    <button type="button" class="quantity-btn minus" aria-label="Riduci quantità">-</button>
                    <input type="number" id="quantita" name="quantita" value="1" min="1" max="' . $disponibilita . '">
                    <button type="button" class="quantity-btn plus" aria-label="Aumenta quantità">+</button>
                </div>
                <span class="available-stock">Disponibili: ' . $disponibilita . '</span>
            </div>
            
            <button type="submit" class="bottone-primario aggiungiCarrello" id="aggiungiCarrello">
                Aggiungi al Carrello
            </button>
        </form>';
    } else {
        $form_carrello_html = '<button class="bottone-primario" disabled>Prodotto Esaurito</button>';
    }
    
    // Query prodotti consigliati
    $sql_consigliati = "SELECT id_prodotto, nome, prezzo, img_path, grammi 
                       FROM prodotto 
                       WHERE categoria = :categoria 
                       AND id_prodotto != :id_prodotto 
                       AND disponibilita > 0
                       LIMIT 3";
    
    $stmt_consigliati = $pdo->prepare($sql_consigliati);
    $stmt_consigliati->execute([
        ':categoria' => $prodotto['categoria'],
        ':id_prodotto' => $id_prodotto
    ]);
    $prodotti_consigliati = $stmt_consigliati->fetchAll(PDO::FETCH_ASSOC);
    
    // Genera HTML prodotti consigliati
    $consigliati_html = '';
    if (!empty($prodotti_consigliati)) {
        $consigliati_html .= '<div class="product-detail-block">';
        $consigliati_html .= '<h2>Prodotti che potrebbero piacerti</h2>';
        $consigliati_html .= '<div class="products-grid catalog-grid recommended-grid">';
        
        foreach ($prodotti_consigliati as $consigliato) {
            $nome_cons = htmlspecialchars($consigliato['nome'], ENT_QUOTES, 'UTF-8');
            $prezzo_cons = number_format($consigliato['prezzo'], 2, ',', '.');
            $img_cons = htmlspecialchars($consigliato['img_path'], ENT_QUOTES, 'UTF-8');
            $grammi_cons = htmlspecialchars($consigliato['grammi'], ENT_QUOTES, 'UTF-8');
            $id_cons = $consigliato['id_prodotto'];
            
            $consigliati_html .= '
            <article class="product-card">
                <div class="product-image">
                    <img src="' . $img_cons . '" alt="' . $nome_cons . '" loading="lazy" onerror="this.src=\'images/placeholder_tea.jpg\'">
                    
                    <form action="php/gestioneCarrello.php" method="POST">
                        <input type="hidden" name="azione" value="aggiungi">
                        <input type="hidden" name="tipo" value="standard">
                        <input type="hidden" name="id_prodotto" value="' . $id_cons . '">
                        <input type="hidden" name="quantita" value="1">
                        
                        <button type="submit" class="add-to-cart-icon" aria-label="Aggiungi ' . $nome_cons . ' al carrello" title="Aggiungi al carrello">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                                <path d="M22.713,4.077A2.993,2.993,0,0,0,20.41,3H4.242L4.2,2.649A3,3,0,0,0,1.222,0H1A1,1,0,0,0,1,2h.222a1,1,0,0,1,.993.883l1.376,11.7A5,5,0,0,0,8.557,19H19a1,1,0,0,0,0-2H8.557a3,3,0,0,1-2.82-2h11.92a5,5,0,0,0,4.921-4.113l.785-4.354A2.994,2.993,0,0,0,22.713,4.077ZM21.4,6.178l-.786,4.354A3,3,0,0,1,17.657,13H5.419L4.478,5H20.41A1,1,0,0,1,21.4,6.178Z"/>
                                <circle cx="7" cy="22" r="2"/>
                                <circle cx="17" cy="22" r="2"/>
                            </svg>
                        </button>
                    </form>
                </div>
                
                <h3>' . $nome_cons . '</h3>
                <p class="product-format">Confezione da ' . $grammi_cons . 'g</p>
                <p class="product-price">€' . $prezzo_cons . '</p>
                
                <div class="product-buttons">
                    <a href="prodotto.php?id=' . $id_cons . '" class="bottone-primario">Scopri di più</a>
                </div>
            </article>';
        }
        
        $consigliati_html .= '</div></div>';
    }
    
} catch (PDOException $e) {
    error_log("Errore prodotto.php: " . $e->getMessage());
    header('Location: catalogo.php');
    exit();
}

// Carica navbar
ob_start();
include 'navbar.php';
$navbar_html = ob_get_clean();

// Carica template
$templatePath = __DIR__ . '/html/prodotto.html';

if (file_exists($templatePath)) {
    $template = file_get_contents($templatePath);
    
    // Sostituzioni
    $template = str_replace('[NAVBAR]', $navbar_html, $template);
    $template = str_replace('[NOME]', $nome, $template);
    $template = str_replace('[DESCRIZIONE_META]', substr($descrizione, 0, 150), $template);
    $template = str_replace('[CATEGORIA]', $categoria, $template);
    $template = str_replace('[CATEGORIA_ENCODED]', urlencode($categoria), $template);
    $template = str_replace('[CATEGORIA_DISPLAY]', $categoria_display, $template);
    $template = str_replace('[IMG_PATH]', $img_path, $template);
    $template = str_replace('[PREZZO]', $prezzo, $template);
    $template = str_replace('[GRAMMI]', $grammi, $template);
    $template = str_replace('[BADGE_DISPONIBILITA]', $badge_disponibilita, $template);
    $template = str_replace('[FORM_CARRELLO]', $form_carrello_html, $template);
    $template = str_replace('[TEMPERATURA]', $temperatura, $template);
    $template = str_replace('[TEMPO]', $tempo, $template);
    $template = str_replace('[DESCRIZIONE]', $descrizione_formattata, $template);
    $template = str_replace('[BASE_NOME]', $base_nome, $template);
    $template = str_replace('[INGREDIENTI_HTML]', $ingredienti_html, $template);
    $template = str_replace('[CONSIGLIATI_HTML]', $consigliati_html, $template);
    
    echo $template;
} else {
    die("Errore: Template non trovato.");
}
?>