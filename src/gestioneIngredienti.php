<?php
require_once 'php/connessione.php';
require_once 'php/verificaSessioneAdmin.php';

// RECUPERO DATI PER VISUALIZZAZIONE (solo SELECT) - altre operazioni nel file aggiornaDisponibilita.php
try {
    $basi = $pdo->query("SELECT * FROM base ORDER BY nome ASC")->fetchAll();
    $ingredienti = $pdo->query("SELECT * FROM ingrediente ORDER BY nome ASC")->fetchAll();
} catch (PDOException $e) {
    die("Errore caricamento dati: " . $e->getMessage());
}

// Genera il contenuto delle card per le basi
$basiHTML = '';
foreach ($basi as $base) {
    $imgTag = !empty($base['img_path']) 
        ? '<img src="' . htmlspecialchars($base['img_path']) . '" alt="" class="admin-card-img">' 
        : '';
    
    $basiHTML .= '
    <article class="admin-card">
        ' . $imgTag . '
        <div class="card-content">
            <h3>' . htmlspecialchars($base['nome']) . '</h3>
            
            <div class="admin-details">
                <p><strong>Stato: </strong>
                    <span class="stato-disponibile"></span> Disponibile
                </p>
                <p><strong>Temp. Infusione:</strong> ' . htmlspecialchars($base['temperatura_infusione']) . '</p>
                <p><strong>Tempo Infusione:</strong> ' . htmlspecialchars($base['tempo_infusione']) . '</p>
            </div>
        </div>
    </article>';
}

// Genera il contenuto delle card per gli ingredienti
$ingredientiHTML = '';
foreach ($ingredienti as $ing) {
    $qtIng = isset($ing['disponibile']) ? $ing['disponibile'] : 0;
    $imgTag = !empty($ing['img_path']) 
        ? '<img src="' . htmlspecialchars($ing['img_path']) . '" alt="" class="admin-card-img">' 
        : '';
    
    $statoHTML = $qtIng > 0 
        ? '<span class="stato-disponibile"></span> Disponibile'
        : '<span class="stato-non-disponibile"></span> Esaurito';
    
    $ingredientiHTML .= '
    <article class="admin-card">
        ' . $imgTag . '
        <div class="card-content">
            <h3>' . htmlspecialchars($ing['nome']) . '</h3>
            
            <div class="admin-details">
                <p><strong>Stato: </strong>' . $statoHTML . '</p>
            </div>

            <form method="POST" action="php/aggiornaDisponibilita.php">
                <input type="hidden" name="id" value="' . $ing['id_ingrediente'] . '">
                <input type="hidden" name="tipo" value="ingrediente">
                
                <fieldset>
                    <legend>Aggiorna scorte</legend>
                    <div class="input-group">
                        <label for="qta_ing' . $ing['id_ingrediente'] . '">Quantità disponibile:</label>
                        <input type="number" 
                               id="qta_ing' . $ing['id_ingrediente'] . '"
                               name="quantita" 
                               value="' . $qtIng . '" 
                               min="0">
                    </div>
                    <input type="submit" class="bottone-primario" value="Aggiorna">
                </fieldset>
            </form>
        </div>
    </article>';
}

// GENERAZIONE MENU ADMIN (Nuova parte)
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

// CARICAMENTO TEMPLATE E SOSTITUZIONI 
$templatePath = 'html/gestioneIngredienti.html';

if (file_exists($templatePath)) {
    $paginaHTML = file_get_contents($templatePath);

    // Sostituzione Menu
    $paginaHTML = str_replace('[menuVoci]', $menuHtml, $paginaHTML);

    // Sostituzione Contenuti
    $paginaHTML = str_replace('[BASI_CONTENT]', $basiHTML, $paginaHTML);
    $paginaHTML = str_replace('[INGREDIENTI_CONTENT]', $ingredientiHTML, $paginaHTML);

    echo $paginaHTML;
} else {
    die("Errore: Template gestioneIngredienti.html non trovato.");
}
?>