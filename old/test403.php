<?php
// Forza errore 403 SENZA popup di autenticazione
http_response_code(403);

// Mostra la tua pagina personalizzata
if (file_exists('errori/403.php')) {
    include('errori/403.php');
} else {
    echo '<h1>403 Unauthorized</h1>';
    echo '<p>Accesso non autorizzato, senza i giusti permessi</p>';
}
exit;
?>