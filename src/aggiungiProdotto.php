<?php
require_once 'php/connessione.php';
require_once 'php/verificaSessioneAdmin.php';

//RECUPERO BASI PER SELECT 
try {
    $stmt = $pdo->query("SELECT id_base, nome FROM base ORDER BY nome ASC");
    $basi = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Errore nel recupero delle basi: " . $e->getMessage());
}

//opzioni select
$opzioni_basi = '';
foreach ($basi as $base) {
    $opzioni_basi .= '<option value="' . $base['id_base'] . '">';
    $opzioni_basi .= htmlspecialchars($base['nome']);
    $opzioni_basi .= '</option>' . "\n";
}

//GENERAZIONE MENU ADMIN
$menuVociAdmin = [
    'dashboardAdmin.php' => '<span lang="en">Dashboard</span>',
    'gestioneProdotti.php' => 'Prodotti',
    'gestioneIngredienti.php' => 'Ingredienti',
    'gestioneOrdini.php' => 'Ordini'
];

$currentPage = basename($_SERVER['PHP_SELF']);
$menuHtml = '';
$leafIcon = '<svg class="nav-leaf-icon" xmlns="http://www.w3.org/2000/svg" viewBox="-77 79 100 100" aria-hidden="true">
                <path d="M-2.5,151.2C13,135.8,16.8,83.6,16.8,83.6s-10.7,6.8-27.5,8.2c-15.8,1.4-30.5,3.6-39.1,12.2c-13.3,13.3-16.6,32.1-9,45.5c10.5-17.8,45-33.5,45-33.5C-38.3,133.4-54.4,150-71.2,174l9.3,1.1c0,0,6.1-11.1,11.5-16.4C-37,168.1-16.6,165.3-2.5,151.2z"/>
            </svg>';

foreach ($menuVociAdmin as $pagina => $testo) {
    $isActive = ($currentPage === $pagina) || ($pagina === 'gestioneProdotti.php');
    $classLi = $isActive ? ' class="current-page"' : '';
    
    if ($isActive) {
        $menuHtml .= "<li$classLi><span class=\"nav-link\">$leafIcon$testo</span></li>\n";
    } else {
        $menuHtml .= "<li$classLi><a href=\"$pagina\" class=\"nav-link\">$testo</a></li>\n";
    }
}

// CARICAMENTO TEMPLATE
$templatePath = 'html/aggiungiProdotto.html';

if (file_exists($templatePath)) {
    $template = file_get_contents($templatePath);
    $template = str_replace('[menuVoci]', $menuHtml, $template);
    $template = str_replace('[OPZIONI_BASI]', $opzioni_basi, $template);

    echo $template;
} else {
    die("Errore: Template non trovato.");
}
?>