<?php
require_once 'php/connessione.php';
require_once 'php/verificaSessioneAdmin.php';

//recupero dati ordini
$ordiniHtml = '';

//query: ordini più recenti per primi
$sql = "SELECT ordine.*, utente.nome, utente.cognome 
        FROM ordine 
        JOIN utente ON ordine.id_utente = utente.id_utente 
        ORDER BY data_ordine DESC";

try {
    $ordini = $pdo->query($sql)->fetchAll();
    
    if (empty($ordini)) {
        $ordiniHtml = '<p>Nessun ordine presente nel sistema.</p>';
    } else {
        foreach ($ordini as $ordine) {
            $stato = $ordine['stato_ord'];
            //mappa Stati -> classi CSS
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
            $dataFmt = date("d/m/Y H:i", strtotime($ordine['data_ordine']));
            $nomeCliente = htmlspecialchars($ordine['nome'] . " " . $ordine['cognome']);
            $totaleFmt = number_format($ordine['totale'], 2);
            
            $ordiniHtml .= <<<HTML
            <article class="order-card">
                <div class="order-info">
                    <h3 class="order-number">Ordine #{$ordine['id_ordine']}</h3>
                    <div class="order-meta">
                        <p><strong>Data:</strong> {$dataFmt}</p>
                        <p><strong>Cliente:</strong> {$nomeCliente}</p>
                    </div>
                </div>

                <div class="order-price">
                    <p>Totale</p>
                    <p>€ {$totaleFmt}</p>
                </div>

                <div class="order-status-action">
                    <div class="status-wrapper">
                        <span class="{$classStato}"></span>
                        <span>{$statoFormattato}</span>
                    </div>
                    <a href="dettaglioOrdineAdmin.php?id={$ordine['id_ordine']}" class="bottone-primario">Vedi dettaglio</a>
                </div>
            </article>
HTML;
        }
    }
    
} catch (PDOException $e) {
    die("Errore recupero ordini: " . $e->getMessage());
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
    $isCurrentPage = ($currentPage === $pagina);
    $classLi = $isCurrentPage ? ' class="current-page"' : '';
    
    if ($isCurrentPage) {
        $menuHtml .= "<li$classLi><span class=\"nav-link\">$leafIcon$testo</span></li>\n";
    } else {
        $menuHtml .= "<li$classLi><a href=\"$pagina\" class=\"nav-link\">$testo</a></li>\n";
    }
}

//CARICAMENTO TEMPLATE
$templatePath = 'html/gestioneOrdini.html';

if (file_exists($templatePath)) {
    $paginaHTML = file_get_contents($templatePath);
    
    $paginaHTML = str_replace('[menuVoci]', $menuHtml, $paginaHTML);
    $paginaHTML = str_replace('[ORDINI_CONTENT]', $ordiniHtml, $paginaHTML); //segnaposto per il main
    
    echo $paginaHTML;
} else {
    die("Errore: template gestioneOrdini.html non trovato");
}
?>