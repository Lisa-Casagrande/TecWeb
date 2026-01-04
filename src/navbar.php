<?php

$loggedIn = $_SESSION['logged_in'] ?? false;
$currentPage = basename($_SERVER['PHP_SELF']);

// CALCOLO TOTALE ARTICOLI NEL CARRELLO
$totale_articoli = 0;
if (isset($_SESSION['carrello'])) {
    foreach ($_SESSION['carrello'] as $item) {
        $totale_articoli += $item['quantita'];
    }
}
?>

<header>
    <div class="header-container">

        <!-- Logo -->
        <div class="logo">
            <a href="home.php" aria-label="Torna alla home" class="logo-button">
                <img src="images/logo/logoChiaro.webp" alt="InfuseMe" class="logo-image logo-light">
                <img src="images/logo/logoScuro.webp" alt="InfuseMe" class="logo-image logo-dark">
            </a>
        </div>

        <!-- Hamburger -->
        <button class="hamburger" id="hamburger" aria-label="Apri il menu navigazione">
            <span></span><span></span><span></span>
        </button>

        <!-- Menu principale -->
        <nav aria-label="Menu principale" role="navigation">
            <ul class="main-nav">

                <li class="<?= $currentPage === 'home.php' ? 'current-page' : '' ?>">
                    <a href="home.php"><span lang="en">Home</span></a>
                </li>

                <li class="<?= $currentPage === 'catalogo.php' ? 'current-page' : '' ?>">
                    <a href="catalogo.php">Catalogo</a>
                </li>

                <li class="<?= $currentPage === 'creaBlend.php' ? 'current-page' : '' ?>">
                    <a href="creaBlend.php">Crea il tuo <span lang="en">Blend</span></a>
                </li>

                <li class="<?= $currentPage === 'chiSiamo.php' ? 'current-page' : '' ?>">
                    <a href="chiSiamo.php">Chi Siamo</a>
                </li>

            </ul>
        </nav>

        <!-- Utility icons -->
        <div class="header-utilities">
            <!-- Ricerca -->
            <button class="icon-button" aria-label="Cerca prodotti">
                <svg class="icon-svg" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 513.749 513.749" aria-hidden="true">
                    <path d="M504.352,459.061l-99.435-99.477c74.402-99.427,54.115-240.344-45.312-314.746S119.261-9.277,44.859,90.15
                             S-9.256,330.494,90.171,404.896c79.868,59.766,189.565,59.766,269.434,0l99.477,99.477c12.501,12.501,32.769,12.501,45.269,0
                             c12.501-12.501,12.501-32.769,0-45.269L504.352,459.061z M225.717,385.696c-88.366,0-160-71.634-160-160s71.634-160,160-160
                             s160,71.634,160,160C385.623,314.022,314.044,385.602,225.717,385.696z"/>
                </svg>
            </button>

            <!-- Carrello -->
            <a href="carrello.php"
   class="icon-button <?= $currentPage === 'carrello.php' ? 'current-page' : '' ?>"
   aria-label="Vai al carrello">
                <svg class="icon-svg" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M22.713,4.077A2.993,2.993,0,0,0,20.41,3H4.242L4.2,2.649A3,3,0,0,0,1.222,0H1A1,1,0,0,0,1,2h.222a1,1,0,0,1,.993.883l1.376,11.7
                             A5,5,0,0,0,8.557,19H19a1,1,0,0,0,0-2H8.557a3,3,0,0,1-2.82-2h11.92a5,5,0,0,0,4.921-4.113l.785-4.354A2.994,2.994,0,0,0,22.713,4.077ZM21.4,6.178
                             l-.786,4.354A3,3,0,0,1,17.657,13H5.419L4.478,5H20.41A1,1,0,0,1,21.4,6.178Z"/>
                    <circle cx="7" cy="22" r="2"/>
                    <circle cx="17" cy="22" r="2"/>
                </svg>
                <!--numero che indica quanti prodotti ci sono nel carrello-->
                <?php if ($totale_articoli > 0): ?>
                    <span class="badge-count"><?= $totale_articoli ?></span>
                <?php endif; ?>
            </a>

            <!-- Area utente / login -->
            <?php if ($loggedIn): ?>
                <!-- Utente loggato -->
                <a href="paginaUtente.php" class="icon-button <?= $currentPage === 'paginaUtente.php' ? 'current-page' : '' ?>" aria-label="Area personale">
                    <svg class="icon-svg" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M12,12A6,6,0,1,0,6,6,6.006,6.006,0,0,0,12,12ZM12,2A4,4,0,1,1,8,6,4,4,0,0,1,12,2Z"/>
                        <path d="M12,14a9.01,9.01,0,0,0-9,9,1,1,0,0,0,2,0,7,7,0,0,1,14,0,1,1,0,0,0,2,0A9.01,9.01,0,0,0,12,14Z"/>
                    </svg>
                </a>
            <?php else: ?>
                <!-- Utente non loggato -->
                <a href="login.php"
       class="icon-button <?= $currentPage === 'login.php' ? 'current-page' : '' ?>" aria-label="Accedi o registrati">
                    <svg class="icon-svg" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M12,12A6,6,0,1,0,6,6,6.006,6.006,0,0,0,12,12ZM12,2A4,4,0,1,1,8,6,4,4,0,0,1,12,2Z"/>
                        <path d="M12,14a9.01,9.01,0,0,0-9,9,1,1,0,0,0,2,0,7,7,0,0,1,14,0,1,1,0,0,0,2,0A9.01,9.01,0,0,0,12,14Z"/>
                    </svg>
                </a>
            <?php endif; ?>

            <!-- Tema chiaro/scuro -->
            <button class="icon-button theme-toggle" aria-label="Cambia tema">
                <!-- Sole -->
                <svg class="theme-icon sun-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M12,17c-2.76,0-5-2.24-5-5s2.24-5,5-5,5,2.24,5,5-2.24,5-5,5Zm0-8c-1.65,0-3,1.35-3,3s1.35,3,3,3,3-1.35,3-3-1.35-3-3-3Zm1-5V1c0-.55-.45-1-1-1s-1,.45-1,1v3c0,.55,.45,1,1,1s1-.45,1-1Zm0,19v-3c0-.55-.45-1-1-1s-1,.45-1,1v3c0,.55,.45,1,1,1s1-.45,1-1ZM5,12c0-.55-.45-1-1-1H1c-.55,0-1,.45-1,1s.45,1,1,1h3c.55,0,1-.45,1-1Zm19,0c0-.55-.45-1-1-1h-3c-.55,0-1,.45-1,1s.45,1,1,1h3c.55,0,1-.45,1-1ZM6.71,6.71c.39-.39,.39-1.02,0-1.41l-2-2c-.39-.39-1.02-.39-1.41,0s-.39,1.02,0,1.41l2,2c.2,.2,.45,.29,.71,.29s.51-.1,.71-.29Zm14,14c.39-.39,.39-1.02,0-1.41l-2-2c-.39-.39-1.02-.39-1.41,0s-.39,1.02,0,1.41l2,2c.2,.2,.45,.29,.71,.29s.51-.1,.71-.29Zm-16,0l2-2c.39-.39,.39-1.02,0-1.41s-1.02-.39-1.41,0l-2,2c-.39,.39-.39,1.02,0,1.41,.2,.2,.45,.29,.71,.29s.51-.1,.71-.29ZM18.71,6.71l2-2c.39-.39,.39-1.02,0-1.41s-1.02-.39-1.41,0l-2,2c-.39,.39-.39,1.02,0,1.41,.2,.2,.45,.29,.71,.29s.51-.1,.71-.29Z"/>
                </svg>
                <!-- Luna -->
                <svg class="theme-icon moon-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M22.386,12.003c-.402-.167-.871-.056-1.151,.28-.928,1.105-2.506,1.62-4.968,1.62-3.814,0-6.179-1.03-6.179-6.158,0-2.397,.532-4.019,1.626-4.957,.33-.283,.439-.749,.269-1.149-.17-.401-.571-.655-1.015-.604C5.285,1.573,1,6.277,1,11.978c0,6.062,4.944,10.993,11.022,10.993,5.72,0,10.438-4.278,10.973-9.951,.042-.436-.205-.848-.609-1.017Z"/>
                </svg>
            </button>
        </div>
    </div>
</header>