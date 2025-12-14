<?php
// Imposta header 500
http_response_code(500);

// MOSTRA la tua pagina di errore personalizzata
include($_SERVER['DOCUMENT_ROOT'] . '/errori/500.html');
exit;
?>