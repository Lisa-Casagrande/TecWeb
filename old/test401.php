<?php
// Forza errore 401 SENZA popup di autenticazione
http_response_code(401);

// Mostra la tua pagina personalizzata
if (file_exists('errori/401.php')) {
    include('errori/401.php');
} else {
    echo '<h1>401 Unauthorized</h1>';
    echo '<p>Accesso non autorizzato</p>';
}
exit;
?>