<?php
//quando admin si logga in login.php si salva informazione nel server
session_start();

//verifica se la variabile di sessione 'admin_logged_in' è settata e vera (per sicurezza)
// se un utente normale scrive nella barra "../dashboardAdmin.php" non avrà la variabile impostata e quindi non può entrare
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: ../login.html");
    exit;
}
//se l'utente non è un admin loggato, è rimandato alla pagina di login (exit ferma il codice dopo x sicurezza)


// da usare ../ perché questo file va dentro php/ (mentre login.html è nella root)
// file da inserire all'inizio di ogni file x admin per garantire sicurezza
?>