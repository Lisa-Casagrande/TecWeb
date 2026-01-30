<?php
// src/creaBlend.php

if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once 'php/connessione.php';

// 1. Logica Navbar
require_once 'php/navbar.php';

// Variabili per l'HTML generato
$basiHtml = '';
$ingredientiHtml = '';

try {
    // --- A. GENERAZIONE HTML BASI ---
    $sqlBasi = "SELECT * FROM base ORDER BY nome";
    $stmtBasi = $pdo->query($sqlBasi);
    
    while ($base = $stmtBasi->fetch()) {
        $id = $base['id_base'];
        $nome = htmlspecialchars($base['nome']);
        // Se nel DB descrizione è null, gestiamo stringa vuota
        $desc = !empty($base['descrizione']) ? '<p class="descrizione">'.htmlspecialchars($base['descrizione']).'</p>' : '';
        $img = htmlspecialchars($base['img_path'] ?? 'images/ingredienti/placeholder.webp');
        $temp = htmlspecialchars($base['temperatura_infusione']);
        $time = htmlspecialchars($base['tempo_infusione']);
        
        $basiHtml .= <<<HTML
        <article class="base-card" data-id="$id" 
                data-nome="$nome"
                data-prezzo="3.50"
                data-temperatura="$temp"
                data-tempo="$time">
            
            <h2 class="base-title">$nome</h2>
            
            <div class="base-content">
                <div class="base-image-standard">
                    <img src="$img" alt="$nome">
                </div>

                <div class="base-info">
                    $desc
                    
                    <div class="info-infusione">
                        <span class="temp">
                            <svg version="1.0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" 
                                width="20" height="20" aria-hidden="true" focusable="false">
                                <g transform="translate(0,512) scale(0.1,-0.1)" fill="currentColor">
                                    <path d="M2379 5100 c-413 -87 -701 -415 -739 -840 -6 -67 -10 -482 -10 -989 l0 -873 -81 -85 c-396 -419 -481 -1054 -208 -1572 67 -128 144 -232 248 -335 542 -540 1407 -538 1945 3 524 526 540 1372 37 1904 l-81 85 0 873 c0 507 -4 922 -10 989 -22 242 -115 441 -284 603 -215 206 -531 298 -817 237z m327 -300 c122 -31 198 -74 290 -165 66 -65 89 -96 122 -165 22 -47 45 -109 51 -138 8 -36 11 -371 11 -1055 l0 -1003 94 -87 c111 -102 179 -190 241 -308 164 -316 165 -681 2 -993 -216 -415 -669 -645 -1126 -571 -340 55 -629 265 -788 571 -163 312 -162 677 2 993 62 118 130 206 241 308 l94 87 0 1003 c0 684 3 1019 11 1055 6 29 29 91 51 138 33 69 56 100 122 165 161 159 371 218 582 165z"/>
                                    <path d="M2130 3885 l0 -175 215 0 215 0 0 -150 0 -150 -215 0 -216 0 3 -132 3 -133 213 -3 212 -2 0 -145 0 -145 -212 -2 -213 -3 -3 -132 -3 -133 216 0 215 0 0 -150 0 -150 -215 0 -215 0 0 -114 0 -113 -39 -27 c-57 -40 -163 -152 -203 -216 -202 -322 -158 -726 109 -993 187 -188 460 -273 711 -221 163 33 302 107 415 221 267 267 311 671 109 993 -40 64 -146 176 -203 216 l-39 27 0 1003 0 1004 -430 0 -430 0 0 -175z"/>
                                </g>
                            </svg>
                            <span class="sr-only">Temperatura: </span>
                            $temp
                        </span>
                        
                        <span class="time">
                            <svg version="1.0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"
                                width="20" height="20" aria-hidden="true" focusable="false">
                                <g transform="translate(0,512) scale(0.1,-0.1)" fill="currentColor">
                                    <path d="M1950 5101 c-51 -26 -80 -74 -80 -130 0 -99 70 -161 182 -161 l58 0 0 -165 0 -165 -27 -6 c-428 -95 -813 -305 -1120 -612 -373 -373 -599 -851 -654 -1381 -14 -140 -7 -426 16 -566 60 -383 221 -751 461 -1058 87 -110 296 -313 415 -402 417 -312 933 -474 1445 -452 587 25 1098 249 1510 661 348 348 564 770 640 1252 22 139 29 425 15 565 -46 441 -214 855 -486 1195 -86 107 -234 257 -328 333 -37 30 -67 57 -67 61 0 4 27 46 61 93 38 55 65 85 72 81 7 -4 45 -30 85 -58 66 -45 78 -50 127 -50 45 0 60 5 92 30 69 56 81 149 27 213 -24 29 -532 384 -583 408 -98 45 -211 -29 -211 -139 0 -70 22 -101 117 -165 l82 -56 -56 -81 c-31 -44 -62 -87 -68 -94 -10 -11 -31 -4 -116 39 -158 79 -340 143 -521 183 l-28 6 0 165 0 165 58 0 c111 0 182 62 182 160 0 32 -7 56 -24 81 -49 71 -28 69 -668 69 -539 0 -575 -1 -608 -19z m750 -436 l0 -145 -140 0 -140 0 0 145 0 145 140 0 140 0 0 -145z m21 -726 c295 -30 576 -135 819 -307 121 -85 305 -270 392 -392 177 -249 286 -553 307 -855 59 -850 -506 -1594 -1349 -1776 -91 -20 -135 -23 -325 -24 -192 0 -234 3 -329 23 -311 66 -592 208 -811 411 -324 301 -514 693 -544 1123 -28 392 80 776 308 1099 85 120 270 305 391 391 335 237 745 348 1141 307z"/>
                                    <path d="M2280 3611 c-398 -85 -752 -353 -938 -710 -109 -208 -154 -395 -155 -631 -1 -338 100 -624 314 -886 201 -246 518 -428 833 -479 537 -87 1069 144 1371 595 115 173 178 335 219 573 l7 37 -703 0 c-784 0 -747 -3 -795 69 l-23 34 0 708 0 709 -22 -1 c-13 0 -61 -9 -108 -18z"/>
                                    <path d="M2710 3025 l0 -605 610 0 610 0 -6 33 c-52 312 -167 546 -376 762 -200 208 -454 348 -728 399 -45 9 -88 16 -96 16 -12 0 -14 -95 -14 -605z"/>
                                </g>
                            </svg>
                            <span class="sr-only">Tempo: </span>
                            $time
                        </span>
                    </div>

                    <button class="btn btn-seleziona-base">Seleziona $nome </button>
                </div>
            </div>
        </article>
HTML;
    }
    
    // --- B. GENERAZIONE HTML INGREDIENTI ---
    $sqlIng = "SELECT * FROM ingrediente ORDER BY tipo, nome";
    $stmtIng = $pdo->query($sqlIng);
    $ingredientiRaw = $stmtIng->fetchAll();
    
    // Raggruppa per tipo
    $gruppi = [];
    foreach ($ingredientiRaw as $ing) {
        $gruppi[$ing['tipo']][] = $ing;
    }
    
    // Funzione titoli (identica alla tua logica)
    function getTitoloTipo($tipo) {
        $map = [
            'frutto' => 'Frutti e Bacche',
            'spezia' => 'Spezie e Radici',
            'fiore' => 'Fiori e Erbe',
            'dolcificante' => 'Dolcificanti Naturali',
            'note' => 'Note Particolari'
        ];
        return $map[$tipo] ?? ucfirst($tipo);
    }
    
    foreach ($gruppi as $tipo => $listaIngredienti) {
        $titolo = getTitoloTipo($tipo);
        
        // Costruiamo il blocco completo per Categoria: Titolo + Griglia + Card
        $ingredientiHtml .= '<h3 class="titolo-categoria">' . $titolo . '</h3>';
        $ingredientiHtml .= '<div class="ingredienti-grid">';
        
        foreach ($listaIngredienti as $ing) {
            $id = $ing['id_ingrediente'];
            $nome = htmlspecialchars($ing['nome']);
            $img = !empty($ing['img_path']) ? $ing['img_path'] : 'images/ingredienti/placeholder.webp';
            $descrizioneIng = !empty($ing['descrizione']) ? '<p class="descrizione-ingrediente">'.htmlspecialchars($ing['descrizione']).'</p>' : '';
            
            // Card Ingrediente (struttura originale)
            $ingredientiHtml .= <<<HTML
            <div class="ingrediente-card" data-id="$id" 
                                        data-nome="$nome"
                                        data-tipo="$tipo"
                                        data-prezzo="1.50">
                
                <img src="$img" alt="$nome">
                <h4>$nome</h4>
                $descrizioneIng
                
                <button class="btn btn-aggiungi-ingrediente">Aggiungi $nome</button>
            </div>
HTML;
        }
        
        $ingredientiHtml .= '</div>'; // Chiude ingredienti-grid
    }
    
} catch (PDOException $e) {
    error_log("Errore creaBlend: " . $e->getMessage());
    $basiHtml = "<p class='error'>Impossibile caricare le basi.</p>";
}

// 2. CARICAMENTO TEMPLATE
$templatePath = 'html/creaBlend.html';

if (file_exists($templatePath)) {
    $template = file_get_contents($templatePath);
    
    // 3. SOSTITUZIONI
    $template = str_replace('[navbar]', $navbarBlock, $template);
    $template = str_replace('[LISTA_BASI]', $basiHtml, $template);
    $template = str_replace('[LISTA_INGREDIENTI]', $ingredientiHtml, $template);
    
    echo $template;
} else {
    die("Errore: Template creaBlend.html non trovato.");
}
?>
