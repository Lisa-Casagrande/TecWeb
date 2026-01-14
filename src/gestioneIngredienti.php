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

$paginaHTML = file_get_contents('html/gestioneIngredienti.html');

// placeholder
$paginaHTML = str_replace('[BASI_CONTENT]', $basiHTML, $paginaHTML);
$paginaHTML = str_replace('[INGREDIENTI_CONTENT]', $ingredientiHTML, $paginaHTML);

echo $paginaHTML;
?>