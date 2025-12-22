<?php
//connessione al DB
require_once 'php/connessione.php';
require_once 'php/verificaSessioneAdmin.php';

//QUERY che recupera ordini e nome utente, ordinati per data decrescente (dal più recente)
$sql = "SELECT ordine.*, utente.nome, utente.cognome 
        FROM ordine 
        JOIN utente ON ordine.id_utente = utente.id_utente 
        ORDER BY data_ordine DESC";

try {
    $ordini = $pdo->query($sql)->fetchAll();
} catch (PDOException $e) {
    die("Errore recupero ordini: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta lang="it" xml:lang="it" xmlns="http://www.w3.org/1999/xhtml">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0"/>
    <title>Ordini dei Clienti - Admin</title>
    <link rel="stylesheet" href="style.css" type="text/css">
</head>

<body>
    <a href="#main-content" class="skip-link">Salta al contenuto principale</a>

    <header>
        <div class="header-container">
            <div class="logo">
                <img src="images/logo/logoChiaro.webp" alt="InfuseMe" class="logo-image logo-light">
                <img src="images/logo/logoScuro1.webp" alt="InfuseMe" class="logo-image logo-dark">
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

            <!-- Utility Icons per l'admin: no carrello e ricerca-->
            <div class="header-utilities">
                <a href="paginaUtente.html" class="icon-button" aria-label="Accedi all'area personale">
                    <!--icona dell'user per area personale, login, registrazione-->
                    <svg class="icon-svg" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M12,12A6,6,0,1,0,6,6,6.006,6.006,0,0,0,12,12ZM12,2A4,4,0,1,1,8,6,4,4,0,0,1,12,2Z"/>
                        <path d="M12,14a9.01,9.01,0,0,0-9,9,1,1,0,0,0,2,0,7,7,0,0,1,14,0,1,1,0,0,0,2,0A9.01,9.01,0,0,0,12,14Z"/>
                    </svg>
                </a>

                <!-- icone per modalità chiara/scura -->
                <button class="icon-button theme-toggle" aria-label="Cambia tema">
                    <!-- Icona sole -->
                    <svg class="theme-icon sun-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M12,17c-2.76,0-5-2.24-5-5s2.24-5,5-5,5,2.24,5,5-2.24,5-5,5Zm0-8c-1.65,0-3,1.35-3,3s1.35,3,3,3,3-1.35,3-3-1.35-3-3-3Zm1-5V1c0-.55-.45-1-1-1s-1,.45-1,1v3c0,.55,.45,1,1,1s1-.45,1-1Zm0,19v-3c0-.55-.45-1-1-1s-1,.45-1,1v3c0,.55,.45,1,1,1s1-.45,1-1ZM5,12c0-.55-.45-1-1-1H1c-.55,0-1,.45-1,1s.45,1,1,1h3c.55,0,1-.45,1-1Zm19,0c0-.55-.45-1-1-1h-3c-.55,0-1,.45-1,1s.45,1,1,1h3c.55,0,1-.45,1-1ZM6.71,6.71c.39-.39,.39-1.02,0-1.41l-2-2c-.39-.39-1.02-.39-1.41,0s-.39,1.02,0,1.41l2,2c.2,.2,.45,.29,.71,.29s.51-.1,.71-.29Zm14,14c.39-.39,.39-1.02,0-1.41l-2-2c-.39-.39-1.02-.39-1.41,0s-.39,1.02,0,1.41l2,2c.2,.2,.45,.29,.71,.29s.51-.1,.71-.29Zm-16,0l2-2c.39-.39,.39-1.02,0-1.41s-1.02-.39-1.41,0l-2,2c-.39,.39-.39,1.02,0,1.41,.2,.2,.45,.29,.71,.29s.51-.1,.71-.29ZM18.71,6.71l2-2c.39-.39,.39-1.02,0-1.41s-1.02-.39-1.41,0l-2,2c-.39,.39-.39,1.02,0,1.41,.2,.2,.45,.29,.71,.29s.51-.1,.71-.29Z"/>
                    </svg>

                    <!-- Icona luna -->
                    <svg class="theme-icon moon-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M22.386,12.003c-.402-.167-.871-.056-1.151,.28-.928,1.105-2.506,1.62-4.968,1.62-3.814,0-6.179-1.03-6.179-6.158,0-2.397,.532-4.019,1.626-4.957,.33-.283,.439-.749,.269-1.149-.17-.401-.571-.655-1.015-.604C5.285,1.573,1,6.277,1,11.978c0,6.062,4.944,10.993,11.022,10.993,5.72,0,10.438-4.278,10.973-9.951,.042-.436-.205-.848-.609-1.017Z"/>
                    </svg>
                </button>
            </div><!--fine header utilities icons-->
        </div>
    </header>

<!-- Main Content -->
    <main id="main-content" role="main">

        <h1>Gestione Ordini Clienti</h1>
        <p>Visualizza lo storico degli ordini, monitora lo stato delle spedizioni e accedi ai dettagli.</p>

        <div class="order-list">
            <?php foreach ($ordini as $ordine): 
                $stato = $ordine['stato_ord'];
                
                // Mappa Stati -> Classi CSS
                $mappaStati = [
                    'annullato' => 'stato-rosso',
                    'in_attesa' => 'stato-giallo',
                    'in_preparazione' => 'stato-giallo',
                    'pagato' => 'stato-verde',
                    'spedito' => 'stato-verde',
                    'consegnato' => 'stato-blu'
                ];

                // Se lo stato non è nella mappa, usa giallo come fallback
                $classStato = isset($mappaStati[$stato]) ? $mappaStati[$stato] : 'stato-giallo';

                // Formattazione stringa stato (toglie trattino basso e mette maiuscola iniziale)
                $statoFormattato = ucfirst(str_replace('_', ' ', $stato));
            ?>
            
            <article class="order-card">
                <div class="order-info">
                    <h3 class="order-number">Ordine #<?php echo $ordine['id_ordine']; ?></h3>
                    <div class="order-meta">
                        <p><strong>Data:</strong> <?php echo date("d/m/Y H:i", strtotime($ordine['data_ordine'])); ?></p>
                        <p><strong>Cliente:</strong> <?php echo htmlspecialchars($ordine['nome'] . " " . $ordine['cognome']); ?></p>
                    </div>
                </div>

                <div class="order-price">
                    <p>Totale</p>
                    <p>€ <?php echo number_format($ordine['totale'], 2); ?></p>
                </div>

                <div class="order-status-action">
                    <div class="status-wrapper">
                        <span class="<?php echo $classStato; ?>"></span>
                        <span><?php echo $statoFormattato; ?></span>
                    </div>
                    <a href="dettaglioOrdineAdmin.php?id=<?php echo $ordine['id_ordine']; ?>" class="bottone-primario">Vedi dettaglio</a>
                </div>
            </article>
            <?php endforeach; ?>
        </div>
    </main>


    <!-- Footer ridotto-->
    <footer>
        <div class="container">
            <div class="footer-section">
                <div class="footer-brand">
                    <div class="brand-name"><span lang="en">InfuseMe</span></div>
                    <div class="motto-brand"><span lang="en">Taste Tradition</span></div>
                </div>
            </div>
        </div>
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