<?php
require_once 'php/connessione.php';
require_once 'php/verificaSessioneAdmin.php';

// Controllo ID
if (!isset($_GET['id'])) {
    header("Location: gestioneOrdini.php");
    exit;
}
$id_ordine = intval($_GET['id']);

// 1. GESTIONE CAMBIO STATO (POST) (piccola form)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nuovo_stato'])) {
    $nuovo_stato = $_POST['nuovo_stato'];
    try {
        $stmtUpdate = $pdo->prepare("UPDATE ordine SET stato_ord = ? WHERE id_ordine = ?");
        $stmtUpdate->execute([$nuovo_stato, $id_ordine]);
        header("Location: dettaglioOrdineAdmin.php?id=$id_ordine");
        exit;
    } catch (PDOException $e) {
        $errore = "Errore: " . $e->getMessage();
    }
}

// 2. RECUPERO DATI ORDINE + UTENTE
$sqlOrdine = "SELECT o.*, u.nome, u.cognome, u.email 
              FROM ordine o 
              JOIN utente u ON o.id_utente = u.id_utente 
              WHERE o.id_ordine = ?";
$stmt = $pdo->prepare($sqlOrdine);
$stmt->execute([$id_ordine]);
$ordine = $stmt->fetch();

if (!$ordine) die("Ordine non trovato.");

// 3. RECUPERO DETTAGLI (Prodotti Standard + Custom Blend) - usa LEFT JOIN per capire se è un prodotto catalogo o un custom
$sqlDettagli = "SELECT do.*, 
                       p.nome AS nome_prodotto, p.img_path,
                       pc.nome_blend, pc.grammi AS grammi_custom
                FROM dettaglio_ordine do 
                LEFT JOIN prodotto p ON do.id_prodotto = p.id_prodotto 
                LEFT JOIN prodotto_custom pc ON do.id_custom = pc.id_custom
                WHERE do.id_ordine = ?";
$stmtDet = $pdo->prepare($sqlDettagli);
$stmtDet->execute([$id_ordine]);
$dettagli = $stmtDet->fetchAll();

// Logica colori stato (uguale a gestioneOrdini.php)
$stato = $ordine['stato_ord'];
$mappaStati = [
    'annullato'       => 'stato-rosso',
    'in_attesa'       => 'stato-giallo',
    'in_preparazione' => 'stato-giallo',
    'pagato'          => 'stato-verde',
    'spedito'         => 'stato-verde',
    'consegnato'      => 'stato-blu'
];
$classStato = isset($mappaStati[$stato]) ? $mappaStati[$stato] : 'stato-giallo';
$statoFormattato = ucfirst(str_replace('_', ' ', $stato));
?>


<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta lang="it" xml:lang="it" xmlns="http://www.w3.org/1999/xhtml">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0"/>
    <title>Dettaglio Ordine #<?php echo $id_ordine; ?></title>
    <link rel="stylesheet" href="style.css" type="text/css">
    <link rel="stylesheet" href="print.css" type="text/css" media="print">
</head>

<body>
    <a href="#main-content" class="skip-link">Salta al contenuto principale</a>

    <header>
        <div class="header-container">
            <div class="logo">
                <img src="images/logo/logoChiaro.webp" alt="InfuseMe" class="logo-image logo-light">
                <img src="images/logo/logoScuro.webp" alt="InfuseMe" class="logo-image logo-dark">
            </div>

           <button class="hamburger" id="hamburger" aria-label="Apri il menu navigazione">
                <span></span>
                <span></span>
                <span></span>
            </button>

            <!-- Navigation (menù)-->
            <nav aria-label="Menu principale" role="navigation">
                <ul class="main-nav">
                    <li><a href="dashboardAdmin.php"><span lang="en">Dashboard</span></a></li>
                    <li><a href="gestioneProdotti.php">Prodotti</a></li>
                    <li><a href="gestioneIngredienti.php">Ingredienti</a></li>
                    <li><a href="gestioneOrdini.php" class="current-page" aria-current="page">Ordini</a></li>
                </ul>
            </nav>

            <!-- Utility Icons per l'admin-->
            <div class="header-utilities">
                <a href="php/logout.php" class="icon-button" aria-label="Esci">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M11.5,16A1.5,1.5,0,0,0,10,17.5v.8A2.7,2.7,0,0,1,7.3,21H5.7A2.7,2.7,0,0,1,3,18.3V5.7A2.7,2.7,0,0,1,5.7,3H7.3A2.7,2.7,0,0,1,10,5.7v.8a1.5,1.5,0,0,0,3,0V5.7A5.706,5.706,0,0,0,7.3,0H5.7A5.706,5.706,0,0,0,0,5.7V18.3A5.706,5.706,0,0,0,5.7,24H7.3A5.706,5.706,0,0,0,13,18.3v-.8A1.5,1.5,0,0,0,11.5,16Z"/>
                        <path d="M22.561,9.525,17.975,4.939a1.5,1.5,0,0,0-2.121,2.122l3.411,3.411L7,10.5a1.5,1.5,0,0,0,0,3H7l12.318-.028-3.467,3.467a1.5,1.5,0,0,0,2.121,2.122l4.586-4.586A3.505,3.505,0,0,0,22.561,9.525Z"/>
                    </svg>
                </a>
                
                <button class="icon-button theme-toggle" aria-label="Cambia tema">
                    <svg class="theme-icon sun-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M12,17c-2.76,0-5-2.24-5-5s2.24-5,5-5,5,2.24,5,5-2.24,5-5,5Zm0-8c-1.65,0-3,1.35-3,3s1.35,3,3,3,3-1.35,3-3-1.35-3-3-3Zm1-5V1c0-.55-.45-1-1-1s-1,.45-1,1v3c0,.55,.45,1,1,1s1-.45,1-1Zm0,19v-3c0-.55-.45-1-1-1s-1,.45-1,1v3c0,.55,.45,1,1,1s1-.45,1-1ZM5,12c0-.55-.45-1-1-1H1c-.55,0-1,.45-1,1s.45,1,1,1h3c.55,0,1-.45,1-1Zm19,0c0-.55-.45-1-1-1h-3c-.55,0-1,.45-1,1s.45,1,1,1h3c.55,0,1-.45,1-1ZM6.71,6.71c.39-.39,.39-1.02,0-1.41l-2-2c-.39-.39-1.02-.39-1.41,0s-.39,1.02,0,1.41l2,2c.2,.2,.45,.29,.71,.29s.51-.1,.71-.29Zm14,14c.39-.39,.39-1.02,0-1.41l-2-2c-.39-.39-1.02-.39-1.41,0s-.39,1.02,0,1.41l2,2c.2,.2,.45,.29,.71,.29s.51-.1,.71-.29Zm-16,0l2-2c.39-.39,.39-1.02,0-1.41s-1.02-.39-1.41,0l-2,2c-.39,.39-.39,1.02,0,1.41,.2,.2,.45,.29,.71,.29s.51-.1,.71-.29ZM18.71,6.71l2-2c.39-.39,.39-1.02,0-1.41s-1.02-.39-1.41,0l-2,2c-.39,.39-.39,1.02,0,1.41,.2,.2,.45,.29,.71,.29s.51-.1,.71-.29Z"/>
                    </svg>
                    <svg class="theme-icon moon-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M22.386,12.003c-.402-.167-.871-.056-1.151,.28-.928,1.105-2.506,1.62-4.968,1.62-3.814,0-6.179-1.03-6.179-6.158,0-2.397,.532-4.019,1.626-4.957,.33-.283,.439-.749,.269-1.149-.17-.401-.571-.655-1.015-.604C5.285,1.573,1,6.277,1,11.978c0,6.062,4.944,10.993,11.022,10.993,5.72,0,10.438-4.278,10.973-9.951,.042-.436-.205-.848-.609-1.017Z"/>
                    </svg>
                </button>
            </div>

        </div> <!--fine header container-->
    </header>

<!-- Main Content -->
    <main id="main-content" role="main">
    <section class="admin-dashboard">
        <div class="admin-page-header">
            <div class="header-title-group">
                <h1>Dettaglio Ordine #<?php echo $ordine['id_ordine']; ?></h1>
                <div class="order-status-badge">
                    <span class="<?php echo $classStato; ?>"></span>
                    <strong><?php echo $statoFormattato; ?></strong>
                </div>
            </div>
            
            <a href="gestioneOrdini.php" class="bottone-primario">Torna alla Lista</a>
        </div>

        <?php if (isset($_GET['msg']) && $_GET['msg'] == 'updated'): ?>
            <p class="messaggio-successo">Stato ordine aggiornato con successo!</p>
        <?php endif; ?>

        <div class="admin-grid">
            
            <article class="admin-card">
                <div class="card-content">
                    <h3>Informazioni del Cliente</h3>
                    <div class="admin-details">
                        <p><strong>Nome:</strong> <?php echo htmlspecialchars($ordine['nome'] . " " . $ordine['cognome']); ?></p>
                        <p><strong>Email:</strong> <?php echo htmlspecialchars($ordine['email']); ?></p>
                        
                        <p><strong>Indirizzo di Consegna:</strong> <!--<br> da vedere se andare a capo-->
                        <?php echo htmlspecialchars($ordine['indirizzo_spedizione']); ?></p>
                        
                        <p><strong>Data dell'Ordine:</strong>
                        <?php echo date("d/m/Y H:i", strtotime($ordine['data_ordine'])); ?></p>
                    </div>
                </div>
            </article>

            <article class="admin-card" id="aggiorna-stato">
                <div class="card-content">
                    <h3>Aggiorna Stato</h3>
                    <p>Modifica lo stato attuale dell'ordine.</p>
                    
                    <form method="POST" action="">
                        <fieldset>
                            <legend>Seleziona un nuovo stato</legend>
                            <div class="input-group">
                                <label for="nuovo_stato" class="sr-only">Nuovo Stato</label>
                                <select name="nuovo_stato" id="nuovo_stato">
                                    <option value="in_attesa" <?php if($stato == 'in_attesa') echo 'selected'; ?>>In attesa</option>
                                    <option value="pagato" <?php if($stato == 'pagato') echo 'selected'; ?>>Pagato</option>
                                    <option value="in_preparazione" <?php if($stato == 'in_preparazione') echo 'selected'; ?>>In preparazione</option>
                                    <option value="spedito" <?php if($stato == 'spedito') echo 'selected'; ?>>Spedito</option>
                                    <option value="consegnato" <?php if($stato == 'consegnato') echo 'selected'; ?>>Consegnato</option>
                                    <option value="annullato" <?php if($stato == 'annullato') echo 'selected'; ?>>Annullato</option>
                                </select>
                            </div>
                            <input type="submit" class="bottone-primario" value="Salva Modifica">
                        </fieldset>
                    </form>
                </div>
            </article>

            <article class="admin-card card-full-width">
                <div class="card-content">
                    <h3>Articoli Acquistati</h3>
                    
                    <ul class="product-list">
                        <?php foreach ($dettagli as $item): 
                            $nomeItem = !empty($item['nome_prodotto']) ? $item['nome_prodotto'] : $item['nome_blend'];
                            $tipoItem = !empty($item['nome_prodotto']) ? "Catalogo" : "Blend Custom";
                        ?>
                        <li class="product-item">
                            <div class="product-info">
                                <strong><?php echo htmlspecialchars($nomeItem); ?></strong>
                                <span class="product-type"><?php echo $tipoItem; ?></span>
                            </div>
                            
                            <div class="product-pricing">
                                <span class="qty-badge">x<?php echo $item['quantita']; ?></span>
                                <span class="price">€ <?php echo number_format($item['totale_riga'], 2); ?></span>
                            </div>
                        </li>
                        <?php endforeach; ?>

                        <?php if ($ordine['omaggio'] == 1): ?>
                        <li class="product-item">
                            <div class="product-info">
                                <strong>🎁 Omaggio: <?php echo htmlspecialchars($ordine['descrizione_omaggio']); ?></strong>
                                <span class="product-type">Omaggio per ordini sopra i 50 euro</span>
                            </div>
                            
                            <div class="product-pricing">
                                <span class="qty-badge">x1</span>
                                <span class="price">Gratis</span>
                            </div>
                        </li>
                        <?php endif; ?>
                    </ul>

                    <div class="order-summary">
                        <div class="summary-row">
                            <span>Sottototale</span>
                            <span>€ <?php echo number_format($ordine['sottototale'], 2); ?></span>
                        </div>
                        <div class="summary-row">
                            <span>Spedizione</span>
                            <span>€ <?php echo number_format($ordine['spese_spedizione'], 2); ?></span>
                        </div>
                        <div class="summary-row total-row">
                            <strong>TOTALE</strong>
                            <strong>€ <?php echo number_format($ordine['totale'], 2); ?></strong>
                        </div>
                    </div>

                    <?php if(!empty($ordine['note'])): ?>
                        <div class="order-notes">
                            <strong>Note del cliente:</strong>
                            <p><?php echo htmlspecialchars($ordine['note']); ?></p>
                        </div>
                    <?php endif; ?>
                </div>
            </article>
        </div>
    </section>
    </main>

    
    <!-- Footer ridotto-->
    <footer>
        <div class="container">
            <div class="footer-section-admin">
                <div class="footer-brand">
                    <div class="brand-name"><span lang="en">InfuseMe</span></div>
                    <div class="motto-brand"><span lang="en">Taste Tradition</span></div>
                </div>
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