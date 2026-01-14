<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'php/connessione.php';

$filtro_categorie_html = '';
try {
    $sqlCategorie = "SELECT DISTINCT categoria FROM prodotto ORDER BY categoria";
    $stmtCategorie = $pdo->query($sqlCategorie);
    
    while ($categoria = $stmtCategorie->fetch()) {
        $catNome = htmlspecialchars($categoria['categoria'], ENT_QUOTES, 'UTF-8');
        
        $labelMappa = [
            'tè_nero' => 'Tè Nero',
            'tè_verde' => 'Tè Verde',
            'tè_bianco' => 'Tè Bianco',
            'tè_giallo' => 'Tè Giallo',
            'tè_oolong' => 'Tè Oolong',
            'tisana' => 'Tisane',
            'infuso' => 'Infusi',
            'altro' => 'Kit Speciali'
        ];
        
        $labelVisual = $labelMappa[$catNome] ?? ucfirst(str_replace('_', ' ', $catNome));
        $filtro_categorie_html .= "<label><input type='radio' name='category' value='$catNome'> $labelVisual</label>\n";
    }
} catch (PDOException $e) {
    $filtro_categorie_html = "<p>Errore nel caricamento delle categorie</p>";
}

$filtro_ingredienti_html = '';
try {
    $sqlIngredienti = "SELECT i.id_ingrediente, i.nome, i.tipo, 
                            COUNT(pi.id_prodotto) as conteggio_prodotti
                    FROM ingrediente i
                    LEFT JOIN prodotto_ingrediente pi ON i.id_ingrediente = pi.id_ingrediente
                    WHERE i.tipo IN ('frutto', 'spezia', 'fiore', 'dolcificante', 'note')
                    GROUP BY i.id_ingrediente, i.nome, i.tipo
                    HAVING conteggio_prodotti > 0
                    ORDER BY i.tipo, i.nome";
    
    $stmtIngredienti = $pdo->query($sqlIngredienti);
    
    $ingredientiPerTipo = [];
    while ($ingrediente = $stmtIngredienti->fetch()) {
        $tipo = $ingrediente['tipo'];
        $ingredientiPerTipo[$tipo][] = $ingrediente;
    }
    
    $tipoLabels = [
        'frutto' => 'Frutti e Bacche',
        'spezia' => 'Spezie e Radici',
        'fiore' => 'Fiori e Erbe',
        'dolcificante' => 'Dolcificanti',
        'note' => 'Note Particolari'
    ];
    
    foreach ($tipoLabels as $tipo => $label) {
        if (isset($ingredientiPerTipo[$tipo]) && count($ingredientiPerTipo[$tipo]) > 0) {
            $filtro_ingredienti_html .= "<div class='filter-subgroup'>\n";
            $filtro_ingredienti_html .= "<h5>$label</h5>\n";
            
            foreach ($ingredientiPerTipo[$tipo] as $ingrediente) {
                $idIng = $ingrediente['id_ingrediente'];
                $nomeIng = htmlspecialchars($ingrediente['nome'], ENT_QUOTES, 'UTF-8');
                $nomeIngLower = strtolower($ingrediente['nome']);
                $conteggio = $ingrediente['conteggio_prodotti'];
                
                $filtro_ingredienti_html .= "<label class='checkbox-label'>";
                $filtro_ingredienti_html .= "<input type='checkbox' class='ing-filter' value='$nomeIngLower' data-id='$idIng' data-nome='$nomeIng'>";
                $filtro_ingredienti_html .= "<span>$nomeIng ($conteggio)</span>";
                $filtro_ingredienti_html .= "</label>\n";
            }
            $filtro_ingredienti_html .= "</div>\n";
        }
    }
} catch (PDOException $e) {
    $filtro_ingredienti_html = "<p>Errore nel caricamento degli ingredienti</p>";
}

$prodotti_html = '';
try {
    $sql = "SELECT p.*, 
                   GROUP_CONCAT(DISTINCT i.nome SEPARATOR ', ') as lista_ingredienti,
                   b.nome as nome_base,
                   b.temperatura_infusione,
                   b.tempo_infusione
            FROM prodotto p
            LEFT JOIN prodotto_ingrediente pi ON p.id_prodotto = pi.id_prodotto
            LEFT JOIN ingrediente i ON pi.id_ingrediente = i.id_ingrediente
            LEFT JOIN base b ON p.id_base = b.id_base
            GROUP BY p.id_prodotto
            ORDER BY p.nome";
    
    $stmt = $pdo->query($sql);
    $rowCount = $stmt->rowCount();
    
    if ($rowCount > 0) {
        while ($row = $stmt->fetch()) {
            $cat = strtolower($row['categoria']);
            $ingr = strtolower($row['lista_ingredienti'] ?? '');
            $base = htmlspecialchars($row['nome_base'] ?? 'N/A', ENT_QUOTES, 'UTF-8');
            $nome = htmlspecialchars($row['nome'], ENT_QUOTES, 'UTF-8');
            $prezzo = number_format($row['prezzo'], 2, ',', '.');
            $prezzoRaw = $row['prezzo'];
            $img = htmlspecialchars($row['img_path'], ENT_QUOTES, 'UTF-8');
            $grammi = $row['grammi'];
            $idProdotto = $row['id_prodotto'];
            $temperatura = $row['temperatura_infusione'] ?? 'N/A';
            $tempo = $row['tempo_infusione'] ?? 'N/A';
            
            $prodotti_html .= "
            <article class='product-card' 
                    data-category='$cat' 
                    data-ingredients='$ingr' 
                    data-price='$prezzoRaw' 
                    data-name='$nome'
                    data-base='$base'
                    data-temperatura='$temperatura'
                    data-tempo='$tempo'
                    data-id='$idProdotto'>
                
                <div class='product-image'>
                    <img src='$img' alt='$nome' loading='lazy' onerror=\"this.src='images/placeholder_tea.jpg'\">
                    <form action='php/gestioneCarrello.php' method='POST'>
                        <input type='hidden' name='azione' value='aggiungi'>
                        <input type='hidden' name='tipo' value='standard'>
                        <input type='hidden' name='id_prodotto' value='$idProdotto'>
                        <input type='hidden' name='quantita' value='1'>
                        
                        <button type='submit' class='add-to-cart-icon' 
                                aria-label='Aggiungi $nome al carrello'
                                title='Aggiungi al carrello'>
                            <svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24'>
                                <path d='M22.713,4.077A2.993,2.993,0,0,0,20.41,3H4.242L4.2,2.649A3,3,0,0,0,1.222,0H1A1,1,0,0,0,1,2h.222a1,1,0,0,1,.993.883l1.376,11.7A5,5,0,0,0,8.557,19H19a1,1,0,0,0,0-2H8.557a3,3,0,0,1-2.82-2h11.92a5,5,0,0,0,4.921-4.113l.785-4.354A2.994,2.994,0,0,0,22.713,4.077ZM21.4,6.178l-.786,4.354A3,3,0,0,1,17.657,13H5.419L4.478,5H20.41A1,1,0,0,1,21.4,6.178Z'/>
                                <circle cx='7' cy='22' r='2'/>
                                <circle cx='17' cy='22' r='2'/>
                            </svg>
                        </button>
                    </form>
                </div>
                
                <h3>$nome</h3>
                
                <p class='product-format'>Confezione da {$grammi}g</p>
                
                <p class='product-price'>€$prezzo</p>
                
                <div class='product-buttons'>
                    <a href='prodotto.php?id=$idProdotto' class='bottone-primario'>Scopri di più</a>
                </div>
            </article>
            ";
        }
    } else {
        $prodotti_html = "<div style='text-align:center; padding: 40px;'>
                <h3>Nessun prodotto disponibile</h3>
                <p>Non ci sono prodotti nel database al momento.</p>
              </div>";
    }
} catch (PDOException $e) {
    $prodotti_html = "<div style='text-align:center; padding: 40px; color: red;'>
            <h3>Errore di connessione</h3>
            <p>Errore nel caricamento dei prodotti. Riprova più tardi.</p>
          </div>";
    error_log("Errore catalogo.php: " . $e->getMessage());
}

ob_start();
include 'navbar.php';
$navbar_html = ob_get_clean();

$templatePath = __DIR__ . '/html/catalogo.html';

if (file_exists($templatePath)) {
    $template = file_get_contents($templatePath);
    
    // Sostituzioni
    $template = str_replace('[NAVBAR]', $navbar_html, $template);
    $template = str_replace('[FILTRO_CATEGORIE]', $filtro_categorie_html, $template);
    $template = str_replace('[FILTRO_INGREDIENTI]', $filtro_ingredienti_html, $template);
    $template = str_replace('[PRODOTTI_GRID]', $prodotti_html, $template);
    
    echo $template;
} else {
    die("Errore: Template non trovato.");
}
?>