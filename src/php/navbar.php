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

// Badge carrello
$badgeCarrello = '';
if ($totale_articoli > 0) {
    $badgeCarrello = '<span class="badge-count">' . $totale_articoli . '</span>';
}

// ========== LOGO ==========
$logoHtml = '';
if ($currentPage === 'index.php') {
    // Logo senza link nella home
    $logoHtml = '
    <span class="logo-button" aria-label="Logo InfuseMe">
        <img src="images/logo/logoChiaro.webp" alt="InfuseMe" class="logo-image logo-light">
        <img src="images/logo/logoScuro.webp" alt="InfuseMe" class="logo-image logo-dark">
        <img src="images/logo/logoStampa.png" alt="InfuseMe" class="logo-image logo-print print-only">
    </span>';
} else {
    // Logo con link
    $logoHtml = '
    <a href="index.php" aria-label="Torna alla home" class="logo-button">
        <img src="images/logo/logoChiaro.webp" alt="InfuseMe" class="logo-image logo-light">
        <img src="images/logo/logoScuro.webp" alt="InfuseMe" class="logo-image logo-dark">
        <img src="images/logo/logoStampa.png" alt="InfuseMe" class="logo-image logo-print print-only">
    </a>';
}

// ========== MENU VOCI ==========
$menuVoci = [
    'index.php' => '<span lang="en">Home</span>',
    'catalogo.php' => 'Catalogo',
    'creaBlend.php' => 'Crea il tuo Blend',
    'chiSiamo.php' => 'Chi Siamo'
];

$menuHtml = '';
$leafIcon = '<svg class="nav-leaf-icon" xmlns="http://www.w3.org/2000/svg" viewBox="-77 79 100 100" aria-hidden="true">
                <path d="M-2.5,151.2C13,135.8,16.8,83.6,16.8,83.6s-10.7,6.8-27.5,8.2c-15.8,1.4-30.5,3.6-39.1,12.2c-13.3,13.3-16.6,32.1-9,45.5c10.5-17.8,45-33.5,45-33.5C-38.3,133.4-54.4,150-71.2,174l9.3,1.1c0,0,6.1-11.1,11.5-16.4C-37,168.1-16.6,165.3-2.5,151.2z"/>
            </svg>';

foreach ($menuVoci as $pagina => $testo) {
    $isCurrentPage = ($currentPage === $pagina);
    $classLi = $isCurrentPage ? ' class="current-page"' : '';
    
    if ($isCurrentPage) {
        $menuHtml .= "<li$classLi><span class=\"nav-link\">$leafIcon$testo</span></li>\n";
    } else {
        $menuHtml .= "<li$classLi><a href=\"$pagina\" class=\"nav-link\">$testo</a></li>\n";
    }
}

// ========== CARRELLO ==========
$carrelloHtml = '';
if ($currentPage === 'carrello.php') {
    $carrelloHtml = '
    <span class="icon-button current-page" aria-label="Sei nella pagina carrello">
        <svg class="icon-svg" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true">
            <path d="M22.713,4.077A2.993,2.993,0,0,0,20.41,3H4.242L4.2,2.649A3,3,0,0,0,1.222,0H1A1,1,0,0,0,1,2h.222a1,1,0,0,1,.993.883l1.376,11.7
                    A5,5,0,0,0,8.557,19H19a1,1,0,0,0,0-2H8.557a3,3,0,0,1-2.82-2h11.92a5,5,0,0,0,4.921-4.113l.785-4.354A2.994,2.994,0,0,0,22.713,4.077ZM21.4,6.178
                    l-.786,4.354A3,3,0,0,1,17.657,13H5.419L4.478,5H20.41A1,1,0,0,1,21.4,6.178Z"/>
            <circle cx="7" cy="22" r="2"/>
            <circle cx="17" cy="22" r="2"/>
        </svg>
        ' . $badgeCarrello . '
    </span>';
} else {
    $carrelloHtml = '
    <a href="carrello.php" class="icon-button" aria-label="Vai al carrello">
        <svg class="icon-svg" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true">
            <path d="M22.713,4.077A2.993,2.993,0,0,0,20.41,3H4.242L4.2,2.649A3,3,0,0,0,1.222,0H1A1,1,0,0,0,1,2h.222a1,1,0,0,1,.993.883l1.376,11.7
                    A5,5,0,0,0,8.557,19H19a1,1,0,0,0,0-2H8.557a3,3,0,0,1-2.82-2h11.92a5,5,0,0,0,4.921-4.113l.785-4.354A2.994,2.994,0,0,0,22.713,4.077ZM21.4,6.178
                    l-.786,4.354A3,3,0,0,1,17.657,13H5.419L4.478,5H20.41A1,1,0,0,1,21.4,6.178Z"/>
            <circle cx="7" cy="22" r="2"/>
            <circle cx="17" cy="22" r="2"/>
        </svg>
        ' . $badgeCarrello . '
    </a>';
}

// ========== AREA UTENTE ==========
$utenteHtml = '';
$userIcon = '<svg class="icon-svg" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true">
                <path d="M12,12A6,6,0,1,0,6,6,6.006,6.006,0,0,0,12,12ZM12,2A4,4,0,1,1,8,6,4,4,0,0,1,12,2Z"/>
                <path d="M12,14a9.01,9.01,0,0,0-9,9,1,1,0,0,0,2,0,7,7,0,0,1,14,0,1,1,0,0,0,2,0A9.01,9.01,0,0,0,12,14Z"/>
            </svg>';

if ($loggedIn) {
    // Utente loggato
    if ($currentPage === 'paginaUtente.php') {
        $utenteHtml = '<span class="icon-button current-page" aria-label="Sei nella tua area personale">' . $userIcon . '</span>';
    } else {
        $utenteHtml = '<a href="paginaUtente.php" class="icon-button" aria-label="Area personale">' . $userIcon . '</a>';
    }
} else {
    // Utente non loggato
    if ($currentPage === 'login.php') {
        $utenteHtml = '<span class="icon-button current-page" aria-label="Sei nella pagina di login">' . $userIcon . '</span>';
    } else {
        $utenteHtml = '<a href="login.php" class="icon-button" aria-label="Accedi o registrati">' . $userIcon . '</a>';
    }
}

// CARICAMENTO TEMPLATE E SOSTITUZIONE
$templateNavbar = file_get_contents(__DIR__ . '/../html/navbar.html');

$navbarBlock = str_replace(
    ['[logoHtml]', '[menuVoci]', '[carrelloHtml]', '[utenteHtml]'],
    [$logoHtml, $menuHtml, $carrelloHtml, $utenteHtml],
    $templateNavbar
);
?>