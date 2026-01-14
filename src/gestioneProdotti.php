<?php
require_once 'php/connessione.php';
require_once 'php/verificaSessioneAdmin.php';

// QUERY: prodotto + JOIN per recuperare il nome della base
$sql = "SELECT p.*, b.nome as nome_base 
        FROM prodotto p 
        LEFT JOIN base b ON p.id_base = b.id_base 
        ORDER BY p.id_prodotto DESC";

$stmt = $pdo->query($sql);
$prodotti = $stmt->fetchAll();

// Genera il contenuto delle card prodotti
$prodottiHTML = '';
foreach ($prodotti as $prodotto) {
    $nomeBase = !empty($prodotto['nome_base']) ? htmlspecialchars($prodotto['nome_base']) : 'Nessuna / Standard';
    $catFormattata = ucfirst(str_replace('_', ' ', $prodotto['categoria']));
    $prezzoFmt = number_format($prodotto['prezzo'], 2);
    
    $imgTag = $prodotto['img_path'] 
        ? '<img src="' . htmlspecialchars($prodotto['img_path']) . '" alt="' . htmlspecialchars($prodotto['nome']) . '" class="admin-card-img">'
        : '';
    
    $prodottiHTML .= <<<HTML
    <article class="admin-card">
        {$imgTag}
        
        <div class="card-content">
            <h3>{$prodotto['nome']}</h3>
            
            <div class="admin-desc">
                {$prodotto['descrizione']}
            </div>

            <div class="admin-details">
                <p><strong>Prezzo:</strong> € {$prezzoFmt}</p>
                <p><strong>Formato:</strong> {$prodotto['grammi']}g</p>
                <p><strong>Disponibilità:</strong> {$prodotto['disponibilita']} pz</p>
                <p><strong>Categoria:</strong> {$catFormattata}</p>
                <p><strong>Base:</strong> {$nomeBase}</p>
            </div>
            
            <div class="admin-actions">
                <form action="php/eliminaProdotto.php" method="POST" onsubmit="return confirm('Sei sicuro di voler eliminare questo prodotto? Questa azione è irreversibile.');">
                    <input type="hidden" name="id_prodotto" value="{$prodotto['id_prodotto']}">
                    <input type="submit" class="bottone-primario" value="Elimina">
                </form>
            </div>
        </div>
    </article>
HTML;
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
$templatePath = 'html/gestioneProdotti.html';

if (file_exists($templatePath)) {
    $html = file_get_contents($templatePath);
    $html = str_replace('[menuVoci]', $menuHtml, $html);
    $html = str_replace('[PRODOTTI_CONTENT]', $prodottiHTML, $html);

    echo $html;
} else {
    die("Errore: Template gestioneProdotti.html non trovato.");
}
?>