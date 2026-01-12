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
            <?php if ($currentPage === 'index.php'): ?>
                <!-- Logo senza link nella home -->
                <span class="logo-button" aria-label="Logo InfuseMe">
                    <img src="images/logo/logoChiaro.webp" alt="InfuseMe" class="logo-image logo-light">
                    <img src="images/logo/logoScuro.webp" alt="InfuseMe" class="logo-image logo-dark">
                </span>
            <?php else: ?>
                <!-- Logo con link nelle altre pagine -->
                <a href="index.php" aria-label="Torna alla home" class="logo-button">
                    <img src="images/logo/logoChiaro.webp" alt="InfuseMe" class="logo-image logo-light">
                    <img src="images/logo/logoScuro.webp" alt="InfuseMe" class="logo-image logo-dark">
                </a>
            <?php endif; ?>
        </div>

        <!-- Hamburger -->
        <button class="hamburger" id="hamburger" aria-label="Apri il menu navigazione">
            <span></span><span></span><span></span>
        </button>

                <!-- Menu principale -->
                <nav aria-label="Menu principale" role="navigation">
                    <ul class="main-nav">
                        <li class="<?= $currentPage === 'index.php' ? 'current-page' : '' ?>">
                            <?php if ($currentPage === 'index.php'): ?>
                                <span class="nav-link">
                                    <svg class="nav-leaf-icon" xmlns="http://www.w3.org/2000/svg" viewBox="-77 79 100 100" aria-hidden="true">
                                        <path d="M-2.5,151.2C13,135.8,16.8,83.6,16.8,83.6s-10.7,6.8-27.5,8.2c-15.8,1.4-30.5,3.6-39.1,12.2c-13.3,13.3-16.6,32.1-9,45.5c10.5-17.8,45-33.5,45-33.5C-38.3,133.4-54.4,150-71.2,174l9.3,1.1c0,0,6.1-11.1,11.5-16.4C-37,168.1-16.6,165.3-2.5,151.2z"/>
                                    </svg>
                                    <span lang="en">Home</span>
                                </span>
                            <?php else: ?>
                                <a href="index.php" class="nav-link">
                                    <span lang="en">Home</span>
                                </a>
                            <?php endif; ?>
                        </li>

                        <li class="<?= $currentPage === 'catalogo.php' ? 'current-page' : '' ?>">
                            <?php if ($currentPage === 'catalogo.php'): ?>
                                <span class="nav-link">
                                    <svg class="nav-leaf-icon" xmlns="http://www.w3.org/2000/svg" viewBox="-77 79 100 100" aria-hidden="true">
                                        <path d="M-2.5,151.2C13,135.8,16.8,83.6,16.8,83.6s-10.7,6.8-27.5,8.2c-15.8,1.4-30.5,3.6-39.1,12.2c-13.3,13.3-16.6,32.1-9,45.5c10.5-17.8,45-33.5,45-33.5C-38.3,133.4-54.4,150-71.2,174l9.3,1.1c0,0,6.1-11.1,11.5-16.4C-37,168.1-16.6,165.3-2.5,151.2z"/>
                                    </svg>
                                    Catalogo
                                </span>
                            <?php else: ?>
                                <a href="catalogo.php" class="nav-link">
                                    Catalogo
                                </a>
                            <?php endif; ?>
                        </li>

                        <li class="<?= $currentPage === 'creaBlend.php' ? 'current-page' : '' ?>">
                            <?php if ($currentPage === 'creaBlend.php'): ?>
                                <span class="nav-link">
                                    <svg class="nav-leaf-icon" xmlns="http://www.w3.org/2000/svg" viewBox="-77 79 100 100" aria-hidden="true">
                                        <path d="M-2.5,151.2C13,135.8,16.8,83.6,16.8,83.6s-10.7,6.8-27.5,8.2c-15.8,1.4-30.5,3.6-39.1,12.2c-13.3,13.3-16.6,32.1-9,45.5c10.5-17.8,45-33.5,45-33.5C-38.3,133.4-54.4,150-71.2,174l9.3,1.1c0,0,6.1-11.1,11.5-16.4C-37,168.1-16.6,165.3-2.5,151.2z"/>
                                    </svg>
                                    Crea il tuo Blend
                                </span>
                            <?php else: ?>
                                <a href="creaBlend.php" class="nav-link">
                                    Crea il tuo Blend
                                </a>
                            <?php endif; ?>
                        </li>

                        <li class="<?= $currentPage === 'chiSiamo.php' ? 'current-page' : '' ?>">
                            <?php if ($currentPage === 'chiSiamo.php'): ?>
                                <span class="nav-link">
                                    <svg class="nav-leaf-icon" xmlns="http://www.w3.org/2000/svg" viewBox="-77 79 100 100" aria-hidden="true">
                                        <path d="M-2.5,151.2C13,135.8,16.8,83.6,16.8,83.6s-10.7,6.8-27.5,8.2c-15.8,1.4-30.5,3.6-39.1,12.2c-13.3,13.3-16.6,32.1-9,45.5c10.5-17.8,45-33.5,45-33.5C-38.3,133.4-54.4,150-71.2,174l9.3,1.1c0,0,6.1-11.1,11.5-16.4C-37,168.1-16.6,165.3-2.5,151.2z"/>
                                    </svg>
                                    Chi Siamo
                                </span>
                            <?php else: ?>
                                <a href="chiSiamo.php" class="nav-link">
                                    Chi Siamo
                                </a>
                            <?php endif; ?>
                        </li>
                    </ul>
                </nav>

        <!-- Utility icons -->
        <div class="header-utilities">
            <!-- Carrello -->
            <?php if ($currentPage === 'carrello.php'): ?>
                <span class="icon-button current-page" aria-label="Sei nella pagina carrello">
                    <svg class="icon-svg" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M22.713,4.077A2.993,2.993,0,0,0,20.41,3H4.242L4.2,2.649A3,3,0,0,0,1.222,0H1A1,1,0,0,0,1,2h.222a1,1,0,0,1,.993.883l1.376,11.7
                                A5,5,0,0,0,8.557,19H19a1,1,0,0,0,0-2H8.557a3,3,0,0,1-2.82-2h11.92a5,5,0,0,0,4.921-4.113l.785-4.354A2.994,2.994,0,0,0,22.713,4.077ZM21.4,6.178
                                l-.786,4.354A3,3,0,0,1,17.657,13H5.419L4.478,5H20.41A1,1,0,0,1,21.4,6.178Z"/>
                        <circle cx="7" cy="22" r="2"/>
                        <circle cx="17" cy="22" r="2"/>
                    </svg>
                    <?php if ($totale_articoli > 0): ?>
                        <span class="badge-count"><?= $totale_articoli ?></span>
                    <?php endif; ?>
                </span>
            <?php else: ?>
                <a href="carrello.php" class="icon-button" aria-label="Vai al carrello">
                    <svg class="icon-svg" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M22.713,4.077A2.993,2.993,0,0,0,20.41,3H4.242L4.2,2.649A3,3,0,0,0,1.222,0H1A1,1,0,0,0,1,2h.222a1,1,0,0,1,.993.883l1.376,11.7
                                A5,5,0,0,0,8.557,19H19a1,1,0,0,0,0-2H8.557a3,3,0,0,1-2.82-2h11.92a5,5,0,0,0,4.921-4.113l.785-4.354A2.994,2.994,0,0,0,22.713,4.077ZM21.4,6.178
                                l-.786,4.354A3,3,0,0,1,17.657,13H5.419L4.478,5H20.41A1,1,0,0,1,21.4,6.178Z"/>
                        <circle cx="7" cy="22" r="2"/>
                        <circle cx="17" cy="22" r="2"/>
                    </svg>
                    <?php if ($totale_articoli > 0): ?>
                        <span class="badge-count"><?= $totale_articoli ?></span>
                    <?php endif; ?>
                </a>
            <?php endif; ?>

            <!-- Area utente / login -->
            <?php if ($loggedIn): ?>
                <!-- Utente loggato -->
                <?php if ($currentPage === 'paginaUtente.php'): ?>
                    <span class="icon-button current-page" aria-label="Sei nella tua area personale">
                        <svg class="icon-svg" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M12,12A6,6,0,1,0,6,6,6.006,6.006,0,0,0,12,12ZM12,2A4,4,0,1,1,8,6,4,4,0,0,1,12,2Z"/>
                            <path d="M12,14a9.01,9.01,0,0,0-9,9,1,1,0,0,0,2,0,7,7,0,0,1,14,0,1,1,0,0,0,2,0A9.01,9.01,0,0,0,12,14Z"/>
                        </svg>
                    </span>
                <?php else: ?>
                    <a href="paginaUtente.php" class="icon-button" aria-label="Area personale">
                        <svg class="icon-svg" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M12,12A6,6,0,1,0,6,6,6.006,6.006,0,0,0,12,12ZM12,2A4,4,0,1,1,8,6,4,4,0,0,1,12,2Z"/>
                            <path d="M12,14a9.01,9.01,0,0,0-9,9,1,1,0,0,0,2,0,7,7,0,0,1,14,0,1,1,0,0,0,2,0A9.01,9.01,0,0,0,12,14Z"/>
                        </svg>
                    </a>
                <?php endif; ?>
            <?php else: ?>
                <!-- Utente non loggato -->

                <?php if ($currentPage === 'login.php'): ?>
                    <span class="icon-button current-page" aria-label="Sei nella pagina di login">
                        <svg class="icon-svg" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M12,12A6,6,0,1,0,6,6,6.006,6.006,0,0,0,12,12ZM12,2A4,4,0,1,1,8,6,4,4,0,0,1,12,2Z"/>
                            <path d="M12,14a9.01,9.01,0,0,0-9,9,1,1,0,0,0,2,0,7,7,0,0,1,14,0,1,1,0,0,0,2,0A9.01,9.01,0,0,0,12,14Z"/>
                        </svg>
                    </span>
                <?php else: ?>
                    <a href="login.php" class="icon-button <?= $currentPage === 'login.php' ? 'current-page' : '' ?>" aria-label="Accedi o registrati">
                    <svg class="icon-svg" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M12,12A6,6,0,1,0,6,6,6.006,6.006,0,0,0,12,12ZM12,2A4,4,0,1,1,8,6,4,4,0,0,1,12,2Z"/>
                        <path d="M12,14a9.01,9.01,0,0,0-9,9,1,1,0,0,0,2,0,7,7,0,0,1,14,0,1,1,0,0,0,2,0A9.01,9.01,0,0,0,12,14Z"/>
                    </svg>
                </a>
                <?php endif; ?>
            <?php endif; ?>

            <!-- Ricerca -->
            <button class="icon-button" aria-label="Cerca prodotti">
                <svg class="icon-svg" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M23.55,21.44l-4.64-4.65c3.47-4.64,2.53-11.23-2.12-14.7S5.56-.43,2.09,4.21s-2.53,11.23,2.12,14.7
                            c3.73,2.79,8.85,2.79,12.58,0l4.65,4.65c0.58,0.58,1.53,0.58,2.11,0s0.58-1.53,0-2.11L23.55,21.44z M10.54,18.01
                            c-4.13,0-7.47-3.34-7.47-7.47s3.34-7.47,7.47-7.47s7.47,3.34,7.47,7.47C18.00,14.66,14.67,17.99,10.54,18.01z"/>
                </svg>
            </button>

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