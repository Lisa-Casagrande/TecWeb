<?php
require_once 'php/connessione.php';

// Verifica se è stato passato un ID prodotto
$id_prodotto = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id_prodotto <= 0) {
    header('Location: catalogo.php');
    exit();
}

try {
    // Query per ottenere i dettagli del prodotto CON IMMAGINI DEGLI INGREDIENTI
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
    
    // Processa gli ingredienti con immagini
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
    
    // Escape dei dati per sicurezza
    $nome = htmlspecialchars($prodotto['nome'], ENT_QUOTES, 'UTF-8');
    $descrizione = htmlspecialchars($prodotto['descrizione'], ENT_QUOTES, 'UTF-8');
    $img_path = htmlspecialchars($prodotto['img_path'], ENT_QUOTES, 'UTF-8');
    $prezzo = number_format($prodotto['prezzo'], 2, ',', '.');
    $prezzo_raw = $prodotto['prezzo'];
    $grammi = htmlspecialchars($prodotto['grammi'], ENT_QUOTES, 'UTF-8');
    $categoria = htmlspecialchars($prodotto['categoria'], ENT_QUOTES, 'UTF-8');
    $ingredienti_lista = !empty($lista_ingredienti_nomi) ? implode(', ', $lista_ingredienti_nomi) : 'Non specificati';
    $base_nome = htmlspecialchars($prodotto['nome_base'] ?? 'Non specificata', ENT_QUOTES, 'UTF-8');
    $base_img = !empty($prodotto['img_base']) ? htmlspecialchars($prodotto['img_base'], ENT_QUOTES, 'UTF-8') : 'images/ingredienti/default-base.webp';
    $temperatura = htmlspecialchars($prodotto['temperatura_infusione'] ?? 'Non specificata', ENT_QUOTES, 'UTF-8');
    $tempo = htmlspecialchars($prodotto['tempo_infusione'] ?? 'Non specificato', ENT_QUOTES, 'UTF-8');
    $disponibilita = $prodotto['disponibilita'];
    
    // Query per ottenere prodotti consigliati della stessa categoria (escludendo il prodotto corrente)
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
    
} catch (PDOException $e) {
    error_log("Errore prodotto.php: " . $e->getMessage());
    header('Location: catalogo.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="it" xml:lang="it" xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0"/>
    <title><?php echo $nome; ?> - InfuseMe</title>
    <meta name="description" content="<?php echo substr($descrizione, 0, 150); ?>">
    <meta name="keywords" content="InfuseMe, tè, tisana, infusi, <?php echo $categoria; ?>, <?php echo $nome; ?>">
    <link rel="stylesheet" href="style.css" type="text/css"/>
</head>

<body>
    <!-- Skip link per accessibilità -->
    <a href="#main-content" class="skip-link">Salta al contenuto principale</a>

    <!-- Header -->
    <header>
        <div class="header-container">
            <div class="logo">
                <a href="home.php" aria-label="Torna alla home" class="logo-button">
                    <img src="images/logo/logoChiaro.webp" alt="InfuseMe" class="logo-image logo-light">
                    <img src="images/logo/logoScuro.webp" alt="InfuseMe" class="logo-image logo-dark">
                </a>
            </div>

            <!-- Pulsante hamburger -->
            <button class="hamburger" id="hamburger" aria-label="Apri il menu navigazione">
                <span></span>
                <span></span>
                <span></span>
            </button>
            
            <!-- Navigation -->
            <nav aria-label="Menu principale" role="navigation">
                <ul class="main-nav">
                    <li><a href="home.html">Home</a></li>
                    <li><a href="catalogo.php" class="current-page" aria-current="page">Catalogo</a></li>
                    <li><a href="creaBlend.html">Crea il tuo <span lang="en">Blend</span></a></li>
                    <li><a href="chiSiamo.html">Chi Siamo</a></li>
                </ul>
            </nav>

            <!-- Utility Icons -->
            <div class="header-utilities">
                <button class="icon-button" aria-label="Cerca prodotti">
                    <svg class="icon-svg" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 513.749 513.749" aria-hidden="true">
                        <path d="M504.352,459.061l-99.435-99.477c74.402-99.427,54.115-240.344-45.312-314.746S119.261-9.277,44.859,90.15 S-9.256,330.494,90.171,404.896c79.868,59.766,189.565,59.766,269.434,0l99.477,99.477c12.501,12.501,32.769,12.501,45.269,0 c12.501-12.501,12.501-32.769,0-45.269L504.352,459.061z M225.717,385.696c-88.366,0-160-71.634-160-160s71.634-160,160-160 s160,71.634,160,160C385.623,314.022,314.044,385.602,225.717,385.696z"/>
                    </svg>
                </button>
                
                <a href="carrello.php" class="icon-button" aria-label="Vai al carrello">
                    <svg class="icon-svg" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M22.713,4.077A2.993,2.993,0,0,0,20.41,3H4.242L4.2,2.649A3,3,0,0,0,1.222,0H1A1,1,0,0,0,1,2h.222a1,1,0,0,1,.993.883l1.376,11.7A5,5,0,0,0,8.557,19H19a1,1,0,0,0,0-2H8.557a3,3,0,0,1-2.82-2h11.92a5,5,0,0,0,4.921-4.113l.785-4.354A2.994,2.993,0,0,0,22.713,4.077ZM21.4,6.178l-.786,4.354A3,3,0,0,1,17.657,13H5.419L4.478,5H20.41A1,1,0,0,1,21.4,6.178Z"/>
                        <circle cx="7" cy="22" r="2"/>
                        <circle cx="17" cy="22" r="2"/>
                    </svg>
                </a>
                
                <a href="paginaUtente.html" class="icon-button" aria-label="Accedi all'area personale">
                    <svg class="icon-svg" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M12,12A6,6,0,1,0,6,6,6.006,6.006,0,0,0,12,12ZM12,2A4,4,0,1,1,8,6,4,4,0,0,1,12,2Z"/>
                        <path d="M12,14a9.01,9.01,0,0,0-9,9,1,1,0,0,0,2,0,7,7,0,0,1,14,0,1,1,0,0,0,2,0A9.01,9.01,0,0,0,12,14Z"/>
                    </svg>
                </a>

                <!-- Tema chiaro/scuro -->
                <button class="icon-button theme-toggle" aria-label="Cambia tema">
                    <svg class="theme-icon sun-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M12,17c-2.76,0-5-2.24-5-5s2.24-5,5-5,5,2.24,5,5-2.24,5-5,5Zm0-8c-1.65,0-3,1.35-3,3s1.35,3,3,3,3-1.35,3-3-1.35-3-3-3Zm1-5V1c0-.55-.45-1-1-1s-1,.45-1,1v3c0,.55,.45,1,1,1s1-.45,1-1Zm0,19v-3c0-.55-.45-1-1-1s-1,.45-1,1v3c0,.55,.45,1,1,1s1-.45,1-1ZM5,12c0-.55-.45-1-1-1H1c-.55,0-1,.45-1,1s.45,1,1,1h3c.55,0,1-.45,1-1Zm19,0c0-.55-.45-1-1-1h-3c-.55,0-1,.45-1,1s.45,1,1,1h3c.55,0,1-.45,1-1ZM6.71,6.71c.39-.39,.39-1.02,0-1.41l-2-2c-.39-.39-1.02-.39-1.41,0s-.39,1.02,0,1.41l2,2c.2,.2,.45,.29,.71,.29s.51-.1,.71-.29Zm14,14c.39-.39,.39-1.02,0-1.41l-2-2c-.39-.39-1.02-.39-1.41,0s-.39,1.02,0,1.41l2,2c.2,.2,.45,.29,.71,.29s.51-.1,.71-.29Zm-16,0l2-2c.39-.39,.39-1.02,0-1.41s-1.02-.39-1.41,0l-2,2c-.39,.39-.39,1.02,0,1.41,.2,.2,.45,.29,.71,.29s.51-.1,.71-.29ZM18.71,6.71l2-2c.39-.39,.39-1.02,0-1.41s-1.02-.39-1.41,0l-2,2c-.39,.39-.39,1.02,0,1.41,.2,.2,.45,.29,.71,.29s.51-.1,.71-.29Z"/>
                    </svg>
                    <svg class="theme-icon moon-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M22.386,12.003c-.402-.167-.871-.056-1.151,.28-.928,1.105-2.506,1.62-4.968,1.62-3.814,0-6.179-1.03-6.179-6.158,0-2.397,.532-4.019,1.626-4.957,.33-.283,.439-.749,.269-1.149-.17-.401-.571-.655-1.015-.604C5.285,1.573,1,6.277,1,11.978c0,6.062,4.944,10.993,11.022,10.993,5.72,0,10.438-4.278,10.973-9.951,.042-.436-.205-.848-.609-1.017Z"/>
                    </svg>
                </button>
            </div>
        </div>
    </header>

    <main id="main-content" role="main">
        <!-- Breadcrumb -->
        <div class="breadcrumb">
            <div class="container">
                <a href="catalogo.php">Catalogo</a> &gt; 
                <a href="catalogo.php?category=<?php echo urlencode($categoria); ?>"><?php echo ucfirst(str_replace('_', ' ', $categoria)); ?></a> &gt; 
                <span><?php echo $nome; ?></span>
            </div>
        </div>

        <!-- Sezione prodotto -->
        <section class="product-detail-section">
            <div class="container product-detail-container">
                <!-- Immagine prodotto -->
                <div class="product-image-column">
                    <div class="product-main-image">
                        <img src="<?php echo $img_path; ?>" 
                             alt="<?php echo $nome; ?>" 
                             onerror="this.src='images/placeholder_tea.jpg'">
                    </div>
                    
                    <?php if ($disponibilita <= 10 && $disponibilita > 0): ?>
                    <div class="availability-badge">
                        Ultimi <?php echo $disponibilita; ?> disponibili!
                    </div>
                    <?php elseif ($disponibilita == 0): ?>
                    <div class="availability-badge out-of-stock">
                        Esaurito
                    </div>
                    <?php endif; ?>
                </div>
                
                <!-- Informazioni prodotto -->
                <div class="product-info-column">
                    <h1><?php echo $nome; ?></h1>
                    
                    <div class="product-meta">
                        <span class="product-category"><?php echo ucfirst(str_replace('_', ' ', $categoria)); ?></span>
                        <span class="product-grams">Confezione da <?php echo $grammi; ?>g</span>
                    </div>
                    
                    <div class="product-price-section">
                        <p class="product-price">€<?php echo $prezzo; ?></p>
                        <p class="product-tax">IVA inclusa</p>
                    </div>
                    
                    <!-- Aggiungi al carrello -->
                    <div class="add-to-cart-section">
                        <?php if ($disponibilita > 0): ?>
                        <div class="quantity-selector">
                            <label for="quantita">Quantità:</label>
                            <div class="quantity-controls">
                                <button type="button" class="quantity-btn minus" aria-label="Riduci quantità">-</button>
                                <input type="number" 
                                       id="quantita" 
                                       name="quantita" 
                                       value="1" 
                                       min="1" 
                                       max="<?php echo $disponibilita; ?>" 
                                       readonly>
                                <button type="button" class="quantity-btn plus" aria-label="Aumenta quantità">+</button>
                            </div>
                            <span class="available-stock">
                                Disponibili: <?php echo $disponibilita; ?>
                            </span>
                        </div>
                        
                        <button class="bottone-primario aggiungiCarrello" 
                                id="aggiungiCarrello"
                                data-id="<?php echo $id_prodotto; ?>"
                                data-nome="<?php echo $nome; ?>"
                                data-prezzo="<?php echo $prezzo_raw; ?>"
                                data-img="<?php echo $img_path; ?>"
                                data-disponibilita="<?php echo $disponibilita; ?>">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20" style="margin-right: 8px;">
                                <path d="M22.713,4.077A2.993,2.993,0,0,0,20.41,3H4.242L4.2,2.649A3,3,0,0,0,1.222,0H1A1,1,0,0,0,1,2h.222a1,1,0,0,1,.993.883l1.376,11.7A5,5,0,0,0,8.557,19H19a1,1,0,0,0,0-2H8.557a3,3,0,0,1-2.82-2h11.92a5,5,0,0,0,4.921-4.113l.785-4.354A2.994,2.993,0,0,0,22.713,4.077ZM21.4,6.178l-.786,4.354A3,3,0,0,1,17.657,13H5.419L4.478,5H20.41A1,1,0,0,1,21.4,6.178Z"/>
                                <circle cx="7" cy="22" r="2"/>
                                <circle cx="17" cy="22" r="2"/>
                            </svg>
                            Aggiungi al Carrello
                        </button>
                        <?php else: ?>
                        <button class="bottone-primario" disabled>
                            Prodotto Esaurito
                        </button>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Informazioni preparazione -->
                    <div class="preparation-info">
                        <div class="preparation-item">
                            <div class="preparation-icon">
                                <svg version="1.0" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 512 512">
                                    <g transform="translate(0,512) scale(0.1,-0.1)" fill="currentColor">
                                        <path d="M2379 5100 c-413 -87 -701 -415 -739 -840 -6 -67 -10 -482 -10 -989 l0 -873 -81 -85 c-396 -419 -481 -1054 -208 -1572 67 -128 144 -232 248 -335 542 -540 1407 -538 1945 3 524 526 540 1372 37 1904 l-81 85 0 873 c0 507 -4 922 -10 989 -22 242 -115 441 -284 603 -215 206 -531 298 -817 237z m327 -300 c122 -31 198 -74 290 -165 66 -65 89 -96 122 -165 22 -47 45 -109 51 -138 8 -36 11 -371 11 -1055 l0 -1003 94 -87 c111 -102 179 -190 241 -308 164 -316 165 -681 2 -993 -216 -415 -669 -645 -1126 -571 -340 55 -629 265 -788 571 -163 312 -162 677 2 993 62 118 130 206 241 308 l94 87 0 1003 c0 684 3 1019 11 1055 6 29 29 91 51 138 33 69 56 100 122 165 161 159 371 218 582 165z"/>
                                        <path d="M2130 3885 l0 -175 215 0 215 0 0 -150 0 -150 -215 0 -216 0 3 -132 3 -133 213 -3 212 -2 0 -145 0 -145 -212 -2 -213 -3 -3 -132 -3 -133 216 0 215 0 0 -150 0 -150 -215 0 -215 0 0 -114 0 -113 -39 -27 c-57 -40 -163 -152 -203 -216 -202 -322 -158 -726 109 -993 187 -188 460 -273 711 -221 163 33 302 107 415 221 267 267 311 671 109 993 -40 64 -146 176 -203 216 l-39 27 0 1003 0 1004 -430 0 -430 0 0 -175z"/>
                                    </g>
                                </svg>
                            </div>
                            <div class="preparation-text">
                                <strong>Temperatura consigliata:</strong>
                                <span><?php echo $temperatura; ?></span>
                            </div>
                        </div>
                        
                        <div class="preparation-item">
                            <div class="preparation-icon">
                                <svg version="1.0" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 512 512">
                                    <g transform="translate(0,512) scale(0.1,-0.1)" fill="currentColor">
                                        <path d="M1950 5101 c-51 -26 -80 -74 -80 -130 0 -99 70 -161 182 -161 l58 0 0 -165 0 -165 -27 -6 c-428 -95 -813 -305 -1120 -612 -373 -373 -599 -851 -654 -1381 -14 -140 -7 -426 16 -566 60 -383 221 -751 461 -1058 87 -110 296 -313 415 -402 417 -312 933 -474 1445 -452 587 25 1098 249 1510 661 348 348 564 770 640 1252 22 139 29 425 15 565 -46 441 -214 855 -486 1195 -86 107 -234 257 -328 333 -37 30 -67 57 -67 61 0 4 27 46 61 93 38 55 65 85 72 81 7 -4 45 -30 85 -58 66 -45 78 -50 127 -50 45 0 60 5 92 30 69 56 81 149 27 213 -24 29 -532 384 -583 408 -98 45 -211 -29 -211 -139 0 -70 22 -101 117 -165 l82 -56 -56 -81 c-31 -44 -62 -87 -68 -94 -10 -11 -31 -4 -116 39 -158 79 -340 143 -521 183 l-28 6 0 165 0 165 58 0 c111 0 182 62 182 160 0 32 -7 56 -24 81 -49 71 -28 69 -668 69 -539 0 -575 -1 -608 -19z m750 -436 l0 -145 -140 0 -140 0 0 145 0 145 140 0 140 0 0 -145z m21 -726 c295 -30 576 -135 819 -307 121 -85 305 -270 392 -392 177 -249 286 -553 307 -855 59 -850 -506 -1594 -1349 -1776 -91 -20 -135 -23 -325 -24 -192 0 -234 3 -329 23 -311 66 -592 208 -811 411 -324 301 -514 693 -544 1123 -28 392 80 776 308 1099 85 120 270 305 391 391 335 237 745 348 1141 307z"/>
                                        <path d="M2280 3611 c-398 -85 -752 -353 -938 -710 -109 -208 -154 -395 -155 -631 -1 -338 100 -624 314 -886 201 -246 518 -428 833 -479 537 -87 1069 144 1371 595 115 173 178 335 219 573 l7 37 -703 0 c-784 0 -747 -3 -795 69 l-23 34 0 708 0 709 -22 -1 c-13 0 -61 -9 -108 -18z"/>
                                        <path d="M2710 3025 l0 -605 610 0 610 0 -6 33 c-52 312 -167 546 -376 762 -200 208 -454 348 -728 399 -45 9 -88 16 -96 16 -12 0 -14 -95 -14 -605z"/>
                                    </g>
                                </svg>
                            </div>
                            <div class="preparation-text">
                                <strong>Tempo di infusione:</strong>
                                <span><?php echo $tempo; ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        
        <!-- Descrizione e dettagli -->
        <section class="product-details-section">
            <div class="container">
                <div class="details-tabs">
                    <button class="tab-button active" data-tab="descrizione">Descrizione</button>
                    <button class="tab-button" data-tab="ingredienti">Ingredienti</button>
                    <button class="tab-button" data-tab="preparazione">Preparazione</button>
                    <?php if (!empty($prodotti_consigliati)): ?>
                    <button class="tab-button" data-tab="abbinamenti">Abbinamenti Consigliati</button>
                    <?php endif; ?>
                </div>
                
                <div class="tab-content">
                    <!-- Descrizione -->
                    <div id="descrizione" class="tab-pane active">
                        <h2>Descrizione</h2>
                        <p><?php echo nl2br($descrizione); ?></p>
                    </div>
                    
                    <!-- Ingredienti -->
                    <div id="ingredienti" class="tab-pane">
                        <h2>Ingredienti</h2>
                        
                        <div class="ingredienti-container">
                            <div class="base-ingrediente">
                                <h3>Base</h3>
                                <div class="ingrediente-item">
                                    <img src="<?php echo $base_img; ?>" 
                                         alt="<?php echo $base_nome; ?>"
                                         onerror="this.src='images/ingredienti/default-base.webp'">
                                    <div class="ingrediente-info">
                                        <h4><?php echo $base_nome; ?></h4>
                                    </div>
                                </div>
                            </div>
                            
                            <?php if (!empty($ingredienti_con_img)): ?>
                            <div class="altri-ingredienti">
                                <h3>Ingredienti aggiuntivi</h3>
                                <div class="ingredienti-grid">
                                    <?php foreach ($ingredienti_con_img as $ingrediente): ?>
                                    <div class="ingrediente-card">
                                        <img src="<?php echo htmlspecialchars($ingrediente['img_path'], ENT_QUOTES, 'UTF-8'); ?>" 
                                             alt="<?php echo htmlspecialchars($ingrediente['nome'], ENT_QUOTES, 'UTF-8'); ?>"
                                             onerror="this.src='images/ingredienti/default-ingrediente.webp'">
                                        <p class="ingrediente-nome"><?php echo htmlspecialchars($ingrediente['nome'], ENT_QUOTES, 'UTF-8'); ?></p>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- Preparazione -->
                    <div id="preparazione" class="tab-pane">
                        <h2>Guida alla Preparazione</h2>
                        <div class="preparation-steps">
                            <div class="step">
                                <span class="step-number">1</span>
                                <p>Porta l'acqua alla temperatura di <strong><?php echo $temperatura; ?></strong></p>
                            </div>
                            <div class="step">
                                <span class="step-number">2</span>
                                <p>Utilizza 1 cucchiaino (circa 2-3g) di prodotto per ogni 200ml d'acqua</p>
                            </div>
                            <div class="step">
                                <span class="step-number">3</span>
                                <p>Lascia in infusione per <strong><?php echo $tempo; ?></strong></p>
                            </div>
                            <div class="step">
                                <span class="step-number">4</span>
                                <p>Rimuovi le foglie e gusta. Puoi aggiungere dolcificante a piacere</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Abbinamenti consigliati -->
                    <?php if (!empty($prodotti_consigliati)): ?>
                    <div id="abbinamenti" class="tab-pane">
                        <h2>Abbinamenti Consigliati</h2>
                        <p>Prodotti che potrebbero piacerti:</p>
                        <div class="recommended-products">
                            <?php foreach ($prodotti_consigliati as $consigliato): 
                                $nome_cons = htmlspecialchars($consigliato['nome'], ENT_QUOTES, 'UTF-8');
                                $prezzo_cons = number_format($consigliato['prezzo'], 2, ',', '.');
                                $img_cons = htmlspecialchars($consigliato['img_path'], ENT_QUOTES, 'UTF-8');
                                $id_cons = $consigliato['id_prodotto'];
                            ?>
                            <div class="recommended-product">
                                <a href="prodotto.php?id=<?php echo $id_cons; ?>">
                                    <img src="<?php echo $img_cons; ?>" alt="<?php echo $nome_cons; ?>">
                                    <h4><?php echo $nome_cons; ?></h4>
                                    <p class="price">€<?php echo $prezzo_cons; ?></p>
                                </a>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="footer-content">
               <!-- Colonna 1: Brand -->
                <div class="footer-section">
                    <div class="footer-brand">
                        <div class="brand-name"><span lang="en">InfuseMe</span></div>
                        <div class="motto-brand"><span lang="en">Taste Tradition</span></div>
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
                            <strong><span lang="en">Customer Care</span>:</strong> +39 111 222 efgh
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

                </div>

            </div> <!--fine footer content-->

            <!-- Social Media Icons -->
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