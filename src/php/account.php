<?php
require_once "php/verifica_sessione.php"; // Protegge la pagina
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Il Mio Account - InfuseMe</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>

<header>
    <h1>InfuseMe</h1>
    <h2>Benvenuto nella tua area personale</h2>
</header>

<nav id="menu">
    <ul>
        <li><a href="index.html">Home</a></li>
        <li><a href="prodotti.html">I Nostri Prodotti</a></li>
        <li><a href="preparazione.html">Preparazione</a></li>
        <li><a href="storia.html">Storia del Tè</a></li>
        <li><a href="account.php" aria-current="page">Il Mio Account</a></li>
        <li><a href="contatti.html">Contatti</a></li>
    </ul>
</nav>

<main id="content">

    <!-- PROFILO UTENTE -->
    <section id="profilo-utente">
        <h2>Il Mio Profilo</h2>

        <div class="user-info">
            <img src="images/avatar-utente.jpg" alt="Immagine profilo utente">

            <div class="user-details">
                <p><strong>Nome:</strong> <?php echo htmlspecialchars($_SESSION['nome']); ?></p>
                <p><strong>Cognome:</strong> <?php echo htmlspecialchars($_SESSION['cognome']); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($_SESSION['email']); ?></p>
                <p><strong>Username:</strong> <?php echo htmlspecialchars($_SESSION['username']); ?></p>
                <p><strong>Registrato dal:</strong> <?php echo htmlspecialchars($_SESSION['data']); ?></p>

                <p><a href="modifica-profilo.php">Modifica profilo</a></p>
            </div>
        </div>
    </section>

    <!-- ORDINI RECENTI (STATICI PER ORA) -->
    <section id="ordini-recenti">
        <h2>I Miei Ordini Recenti</h2>

        <article class="ordine">
            <h3>Ordine #TEA-2024-0012</h3>
            <p><strong>Data:</strong> 15 gennaio 2024</p>
            <p><strong>Stato:</strong> Consegnato</p>
            <p><strong>Totale:</strong> €42,50</p>

            <h4>Prodotti acquistati:</h4>
            <ul>
                <li>Tè Verde Giapponese - 100g</li>
                <li>Tisana Relax - 80g</li>
                <li>Infusore in metallo</li>
            </ul>

            <p><a href="dettaglio-ordine.php?id=TEA-2024-0012">Visualizza dettagli</a></p>
        </article>
    </section>

    <!-- LOGOUT -->
    <div class="logout-container">
        <a class="btn btn-logout" href="php/logout.php">Esci dall'account</a>
    </div>

</main>

<footer>
    <p>Tea & Infusi - <span lang="en">All Rights Reserved</span></p>
    <p><a href="privacy.html">Privacy Policy</a> | 
       <a href="cookies.html">Cookie Policy</a> | 
       <a href="termini.html">Termini e Condizioni</a></p>
</footer>

</body>
</html>
