<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once 'php/connessione.php';

try {
    $sqlBasi = "SELECT * FROM base ORDER BY nome";
    $stmtBasi = $pdo->query($sqlBasi);
    $basi = $stmtBasi->fetchAll();
    
    $sqlIngredienti = "SELECT * FROM ingrediente ORDER BY tipo, nome";
    $stmtIngredienti = $pdo->query($sqlIngredienti);
    $ingredientiRaw = $stmtIngredienti->fetchAll();
    
    $ingredientiPerTipo = [];
    foreach ($ingredientiRaw as $ing) {
        $tipo = $ing['tipo'];
        if (!isset($ingredientiPerTipo[$tipo])) { $ingredientiPerTipo[$tipo] = []; }
        $ingredientiPerTipo[$tipo][] = $ing;
    }
} catch (PDOException $e) {
    error_log("Errore creaBlend.php: " . $e->getMessage());
    $basi = []; $ingredientiPerTipo = [];
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Crea il tuo Blend - InfuseMe</title>
    <link rel="stylesheet" href="style.css" type="text/css"/>
    <link rel="stylesheet" href="print.css" type="text/css" media="print">
</head>
<body>
    <?php include 'navbar.php'; ?>

    <!-- Pulsante Riepilogo Mobile (visibile solo su mobile) -->
    <button id="btnRiepilogoMobile" class="fixed-filter-btn" aria-label="Apri riepilogo blend">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
            <path d="m4,7h3c1.103,0,2-.897,2-2v-3c0-1.103-.897-2-2-2h-3c-1.103,0-2,.897-2,2v3c0,1.103.897,2,2,2Zm-1-5c0-.552.448-1,1-1h3c.552,0,1,.448,1,1v3c0,.552-.448,1-1,1h-3c-.552,0-1-.448-1-1v-3Zm6,12v-3c0-1.103-.897-2-2-2h-3c-1.103,0-2,.897-2,2v3c0,1.103.897,2,2,2h3c1.103,0,2-.897,2-2Zm-6,0v-3c0-.552.448-1,1-1h3c.552,0,1,.448,1,1v3c0,.552-.448,1-1,1h-3c-.552,0-1-.448-1-1Zm10-7h3c1.103,0,2-.897,2-2v-3c0-1.103-.897-2-2-2h-3c-1.103,0-2,.897-2,2v3c0,1.103.897,2,2,2Zm-1-5c0-.552.448-1,1-1h3c.552,0,1,.448,1,1v3c0,.552-.448,1-1,1h-3c-.552,0-1-.448-1-1v-3Zm1.5,3h2c.276,0,.5-.224.5-.5v-2c0-.276-.224-.5-.5-.5h-2c-.276,0-.5.224-.5.5v2c0,.276.224.5.5.5Zm.5-2h1v1h-1v-1Zm10,20.5c0,.276-.224.5-.5.5s-.5-.224-.5-.5c0-1.637-.994-3.026-2.596-3.627l-5.08-1.905c-.195-.073-.324-.26-.324-.468v-5.893c0-.789-.535-1.471-1.244-1.587-.45-.07-.886.046-1.227.336-.337.286-.529.703-.529,1.144v8.424c0,.421-.235.796-.615.979-.379.18-.819.132-1.146-.13,0,0-1.716-1.367-1.719-1.371-.606-.562-1.553-.529-2.115.073-.565.604-.534,1.557.064,2.118l1.633,1.551c.325.309.107.856-.342.856-.127,0-.249-.048-.341-.135l-1.64-1.548c-1-.937-1.048-2.518-.106-3.524.928-.994,2.482-1.054,3.49-.149.003.002,1.698,1.347,1.698,1.347l.138-.066v-8.424c0-.734.321-1.429.881-1.905.561-.476,1.307-.68,2.035-.561,1.188.193,2.084,1.3,2.084,2.573v5.546l4.756,1.784c2.001.75,3.244,2.498,3.244,4.562Z"/>
        </svg>
        <span class="riepilogo-badge" id="badge-riepilogo-mobile">0</span>
    </button>

    <!-- Overlay Modal Mobile -->
    <div class="riepilogo-mobile-overlay" id="riepilogo-mobile-overlay">
        <div class="riepilogo-mobile-content">
            
            <div class="riepilogo-header">
                <h3>Il tuo Blend</h3>
                <button class="btn-chiudi-riepilogo">✕</button>
            </div>
            
            <div class="riepilogo-box-mobile">
                
                <div class="config-group">
                    <span class="label">Ingredienti extra:</span>
                    <div class="radio-box">
                        <label><input type="radio" name="numIngredientiMobile" value="2" checked> 2</label>
                        <label><input type="radio" name="numIngredientiMobile" value="3"> 3</label>
                    </div>
                </div>

                <div class="stato-selezione">
                    <p>Base: <span id="contatore-base-mobile">0/1</span></p>
                    <p>Ingredienti: <span id="contatore-ingredienti-mobile">0/2</span></p>
                </div>

                <button class="btn-reset-link" id="btn-reset-mobile">Svuota tutto</button>

                <!-- Area Riepilogo -->
                <div class="riepilogo-scroll-area">
                    <div class="sezione-riepilogo">
                        <h4>Base selezionata:</h4>
                        <div id="base-selezionata-mobile">
                            <p class="nessuna-selezione">Nessuna base selezionata</p>
                        </div>
                    </div>
                    
                    <div class="sezione-riepilogo">
                        <h4>Ingredienti:</h4>
                        <div id="ingredienti-selezionati-mobile">
                            <p class="nessuna-selezione">Nessun ingrediente aggiunto</p>
                        </div>
                    </div>
                </div>

                <!-- Footer Prezzo e Conferma -->
                <div class="riepilogo-footer">
                    <div class="prezzo-finale">Totale: € <span id="importo-prezzo-mobile">0.00</span></div>
                    <button id="btn-conferma-mobile" class="btn-conferma" disabled>Conferma Blend</button>
                </div>
            </div>
        </div>
    </div>

    <main id="main-content" class="crea-blend-layout">
        <div class="container-grid">
            
            <!-- SIDEBAR UNIFICATA (Desktop) -->
            <aside class="sidebar-config">
                <div class="sticky-sidebar">
                    <div class="riepilogo-header">
                        <h3>Il tuo Blend</h3>
                    </div>

                    <!-- Sezione Personalizza (sopra) -->
                    <div class="config-group">
                        <span class="label">Ingredienti extra:</span>
                        <div class="radio-box">
                            <label><input type="radio" name="numIngredienti" value="2" checked> 2</label>
                            <label><input type="radio" name="numIngredienti" value="3"> 3</label>
                        </div>
                    </div>

                    <div class="stato-selezione">
                        <p>Base: <span id="contatore-base">0/1</span></p>
                        <p>Ingredienti: <span id="contatore-ingredienti">0/2</span></p>
                    </div>

                    <button class="btn-reset-link" id="btn-reset">Svuota tutto</button>
                    
                    <!-- Area scrollabile -->
                    <div class="riepilogo-scroll-area">
                        <div class="sezione-riepilogo">
                            <h4>Base selezionata:</h4>
                            <div id="base-selezionata">
                                <p class="nessuna-selezione">Nessuna base selezionata</p>
                            </div>
                        </div>
                        
                        <div class="sezione-riepilogo">
                            <h4>Ingredienti:</h4>
                            <div id="ingredienti-selezionati">
                                <p class="nessuna-selezione">Nessun ingrediente aggiunto</p>
                            </div>
                        </div>
                    </div>

                    <!-- Footer con prezzo e bottone -->
                    <div class="riepilogo-footer">
                        <div class="prezzo-finale">Totale: € <span id="importo-prezzo">0.00</span></div>
                        <button id="btn-conferma" class="btn-conferma" disabled>Conferma Blend</button>
                    </div>
                </div>
            </aside>

            <!-- Sezione Basi -->
            <section id="basi">
                <h2>Scegli la Tua Base (1 obbligatoria)</h2>
                <p class="descrizione-sezione">Seleziona una base per il tuo blend.</p>
                
                <div class="basi-grid">
                    <?php foreach ($basi as $base): ?>
                    <article class="base-card" data-id="<?php echo $base['id_base']; ?>" 
                            data-nome="<?php echo htmlspecialchars($base['nome']); ?>"
                            data-prezzo="3.50"
                            data-temperatura="<?php echo htmlspecialchars($base['temperatura_infusione']); ?>"
                            data-tempo="<?php echo htmlspecialchars($base['tempo_infusione']); ?>">
                        
                        <h3 class="base-title"><?php echo htmlspecialchars($base['nome']); ?></h3>
                        
                        <div class="base-content">
                            <div class="base-image-standard">
                                <img src="<?php echo $base['img_path'] ?? 'images/ingredienti/placeholder.webp'; ?>" 
                                    alt="<?php echo htmlspecialchars($base['nome']); ?>">
                            </div>

                            <div class="base-info">
                                <?php if (!empty($base['descrizione'])): ?>
                                    <p class="descrizione"><?php echo htmlspecialchars($base['descrizione']); ?></p>
                                <?php endif; ?>
                                
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
                                        <?php echo htmlspecialchars($base['temperatura_infusione']); ?>
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
                                        <?php echo htmlspecialchars($base['tempo_infusione']); ?>
                                    </span>
                                </div>

                                <button class="btn btn-seleziona-base">Seleziona Base</button>
                            </div>
                        </div>
                    </article>
                    <?php endforeach; ?>
                </div>
            </section>

            <!-- Sezione Ingredienti -->
            <section id="ingredienti">
                <h2>Aggiungi gli Ingredienti</h2>
                <p class="descrizione-sezione">Seleziona gli ingredienti per personalizzare il tuo blend.</p>
                
                <?php foreach ($ingredientiPerTipo as $tipo => $ingredienti): 
                    $titoloTipo = '';
                    switch ($tipo) {
                        case 'frutto': $titoloTipo = 'Frutti e Bacche'; break;
                        case 'spezia': $titoloTipo = 'Spezie e Radici'; break;
                        case 'fiore': $titoloTipo = 'Fiori e Erbe'; break;
                        case 'dolcificante': $titoloTipo = 'Dolcificanti Naturali'; break;
                        case 'note': $titoloTipo = 'Note Particolari'; break;
                        default: $titoloTipo = ucfirst($tipo);
                    }
                ?>
                
                <h3 class="titolo-categoria"><?php echo $titoloTipo; ?></h3>
                
                <div class="ingredienti-grid">
                    <?php foreach ($ingredienti as $ing): ?>
                    <div class="ingrediente-card" data-id="<?php echo $ing['id_ingrediente']; ?>" 
                                                data-nome="<?php echo htmlspecialchars($ing['nome']); ?>"
                                                data-tipo="<?php echo $ing['tipo']; ?>"
                                                data-prezzo="1.50">
                        
                        <img src="<?php echo $ing['img_path'] ?? 'images/ingredienti/placeholder.webp'; ?>" 
                            alt="<?php echo htmlspecialchars($ing['nome']); ?>">
                        
                        <h4><?php echo htmlspecialchars($ing['nome']); ?></h4>
                        
                        <?php if (!empty($ing['descrizione'])): ?>
                            <p class="descrizione-ingrediente"><?php echo htmlspecialchars($ing['descrizione'], ENT_QUOTES, 'UTF-8'); ?></p>
                        <?php endif; ?>
                        
                        <button class="btn btn-aggiungi-ingrediente">
                            Aggiungi Ingrediente
                        </button>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endforeach; ?>
            </section>

        </div>
        
        <form id="form-blend" action="php/gestioneCarrello.php" method="POST" style="display: none;">
            <input type="hidden" name="azione" value="aggiungi">
            <input type="hidden" name="id_base" id="input-id-base">
            <input type="hidden" name="ingredienti" id="input-ingredienti">
            <input type="hidden" name="nome_blend" id="input-nome-blend">
            <input type="hidden" name="prezzo" id="input-prezzo">
        </form>
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