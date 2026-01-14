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
    
    $imgTag = $prodotto['img_path'] 
        ? '<img src="' . htmlspecialchars($prodotto['img_path']) . '" alt="' . htmlspecialchars($prodotto['nome']) . '" class="admin-card-img">'
        : '';
    
    $prodottiHTML .= '
    <article class="admin-card">
        ' . $imgTag . '
        
        <div class="card-content">
            <h3>' . htmlspecialchars($prodotto['nome']) . '</h3>
            
            <div class="admin-desc">
                ' . htmlspecialchars($prodotto['descrizione']) . '
            </div>

            <div class="admin-details">
                <p><strong>Prezzo:</strong> € ' . number_format($prodotto['prezzo'], 2) . '</p>
                <p><strong>Formato:</strong> ' . $prodotto['grammi'] . 'g</p>
                <p><strong>Disponibilità:</strong> ' . $prodotto['disponibilita'] . ' pz</p>
                <p><strong>Categoria:</strong> ' . $catFormattata . '</p>
                <p><strong>Base:</strong> ' . $nomeBase . '</p>
            </div>
            
            <div class="admin-actions">
                <form action="php/eliminaProdotto.php" method="POST" onsubmit="return confirm(\'Sei sicuro di voler eliminare ' . htmlspecialchars($prodotto['nome']) . '? Questa azione è irreversibile.\');">
                    <input type="hidden" name="id_prodotto" value="' . $prodotto['id_prodotto'] . '">
                    <input type="submit" class="bottone-primario" value="Elimina">
                </form>
            </div>
        </div>
    </article>';
}

$templatePath = __DIR__ . '/html/gestioneProdotti.html';
$html = file_get_contents($templatePath);

// sostituzione placeholder con i dati dinamici
$html = str_replace('{{PRODOTTI_CONTENT}}', $prodottiHTML, $html);

echo $html;
?>