<?php
session_start();
require_once 'connessione.php';

if (!isset($_SESSION['carrello'])) {
    $_SESSION['carrello'] = [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $azione = $_POST['azione'] ?? 'aggiungi';

    // AZIONE 1: AGGIUNGI
    if ($azione === 'aggiungi') {
        $tipo = $_POST['tipo'] ?? (isset($_POST['nome_blend']) ? 'custom' : 'standard');
        $quantita = (int)($_POST['quantita'] ?? 1);
        
        // LOGICA PRODOTTI CUSTOM (in base al JS)
        if ($tipo === 'custom') {
             $temp_id = 'custom_' . uniqid();
             $chiave_carrello = $temp_id;

             // 1. Recupero Ingredienti: stringa JSON
             $ids_ingredienti = [];
             if (isset($_POST['ingredienti'])) {
                 //decodifica la stringa JSON inviata dal JS
                 $decoded = json_decode($_POST['ingredienti'], true);
                 if (is_array($decoded)) {
                     $ids_ingredienti = $decoded;
                 }
             } elseif (isset($_POST['ids_ingredienti']) && is_array($_POST['ids_ingredienti'])) {
                 // Fallback nel caso arrivasse un array
                 $ids_ingredienti = $_POST['ids_ingredienti'];
             }

             // 2. Recupero Nomi Ingredienti
             $nomi_ingredienti_stringa = $_POST['ingredienti_blend'] ?? '';
             if (empty($nomi_ingredienti_stringa) && !empty($ids_ingredienti)) {
                 //recupera nomi dal DB se mancano
                 $placeholders = implode(',', array_fill(0, count($ids_ingredienti), '?'));
                 $stmtIng = $pdo->prepare("SELECT nome FROM ingrediente WHERE id_ingrediente IN ($placeholders)");
                 $stmtIng->execute($ids_ingredienti);
                 $nomi_array = $stmtIng->fetchAll(PDO::FETCH_COLUMN);
                 $nomi_ingredienti_stringa = implode(", ", $nomi_array);
             }

             // 3. Campi da riempire (in base al JS)
             $prezzo = $_POST['prezzo'] ?? $_POST['prezzo_totale'] ?? 0;
             $id_base = $_POST['id_base'] ?? 0;
             $nome_base = $_POST['nome_base'] ?? '';
             if (empty($nome_base) && $id_base) {
                 $stmtBase = $pdo->prepare("SELECT nome FROM base WHERE id_base = ?");
                 $stmtBase->execute([$id_base]);
                 $nome_base = $stmtBase->fetchColumn() ?: 'Base';
             }

             $_SESSION['carrello'][$chiave_carrello] = [
                'id' => null, 
                'temp_id' => $chiave_carrello, 
                'tipo' => 'custom',
                'nome' => $_POST['nome_blend'] ?? 'Blend Personalizzato',
                'base' => $nome_base,
                'id_base' => $id_base,
                'ingredienti' => $nomi_ingredienti_stringa,
                'ids_ingredienti' => $ids_ingredienti,
                'prezzo' => $prezzo,
                'quantita' => $quantita
            ];
        } 
        // LOGICA PRODOTTO STANDARD
        else {
            $id = $_POST['id_prodotto'] ?? null;
            $chiave_carrello = 'prod_' . $id;

            if ($id) {
                if (isset($_SESSION['carrello'][$chiave_carrello])) {
                    $_SESSION['carrello'][$chiave_carrello]['quantita'] += $quantita;
                } else {
                    $stmt = $pdo->prepare("SELECT nome, prezzo, grammi FROM prodotto WHERE id_prodotto = ?");
                    $stmt->execute([$id]);
                    
                    if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        $_SESSION['carrello'][$chiave_carrello] = [
                            'id' => $id,
                            'tipo' => 'standard',
                            'nome' => $row['nome'],
                            'prezzo' => $row['prezzo'],
                            'grammi' => $row['grammi'],
                            'quantita' => $quantita
                        ];
                    }
                }
            }
        }
    }

    // AZIONE 2: RIMUOVI
    if ($azione === 'rimuovi') {
        $chiave = $_POST['chiave_carrello'];
        if (isset($_SESSION['carrello'][$chiave])) {
            unset($_SESSION['carrello'][$chiave]);
        }
    }

    // AZIONE 3: AGGIORNA
    if ($azione === 'aggiorna') {
        $chiave = $_POST['chiave_carrello'];
        $qty = (int)$_POST['nuova_quantita'];
        
        if ($qty > 0 && isset($_SESSION['carrello'][$chiave])) {
            $_SESSION['carrello'][$chiave]['quantita'] = $qty;
        } elseif ($qty <= 0 && isset($_SESSION['carrello'][$chiave])) {
            unset($_SESSION['carrello'][$chiave]);
        }
    }
}

// RITORNO AL REFERRER
if (strpos($_SERVER['HTTP_REFERER'], 'creaBlend.php') !== false) {
    header("Location: ../carrello.php"); 
} else {
    $referrer = $_SERVER['HTTP_REFERER'] ?? '../catalogo.php';
    header("Location: $referrer");
}
exit;
?>