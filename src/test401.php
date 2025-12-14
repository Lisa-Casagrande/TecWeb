<?php
// Forza errore 401 SENZA popup di autenticazione
http_response_code(401);

// Mostra la tua pagina personalizzata
if (file_exists('errori/401.html')) {
    include('errori/401.html');
} else {
    echo '<h1>401 Unauthorized</h1>';
    echo '<p>Accesso non autorizzato</p>';
}
exit;
?>