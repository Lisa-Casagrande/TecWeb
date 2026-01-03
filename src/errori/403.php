<!DOCTYPE html>
<html lang="it" xml:lang="it" xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0" />
    <title>Errore 403 - InfuseMe</title>
    <meta name="description" content="Errore 403 - Accesso vietato" />
    <meta name="robots" content="noindex, nofollow" />
    <meta name="keywords" content="errore 403, accesso vietato, permesso negato" />
    <link rel="stylesheet" href="style.css" type="text/css" />
</head>
<body>
    <!-- Skip link per accessibilità -->
    <a href="#main-content" class="skip-link">Salta al contenuto principale</a>

    <?php include $_SERVER['DOCUMENT_ROOT'] . '/navbar.php'; ?>

    <!-- CONTENUTO ERRORE 403 -->
    <main id="main-content">
      <div class="errore-content">
        
        <!-- Immagine a sinistra -->
        <div class="img-errore">
          <img src="Images/errori/403.jpg" alt="Cancello che blocca l'entrata. Errore 403 accesso vietato" loading="lazy">
        </div>

        <!-- Testo a destra -->
        <div class="testo-errore">
 <h1>Fermati! Accesso negato</h1>
    <p>Ci dispiace, ma sembra che non ti abbiano fornito le chiavi giuste e senza quelle non riuscirai ad aprire questo cancello!</p>
    <p>Puoi però <a href="/home.html">tornare alla Home</a> o <a href="/login.php">effettuare il login</a> come amministratore per ottenere l'accesso corretto.</p>
    <div class="separatore-testo"></div>
    <p class="ultima-frase">
        Accesso negato: non hai i permessi necessari per visualizzare questa sezione riservata agli utenti autorizzati.
    </p>
</div>

      </div>
    </main>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="footer-content">
                <!-- Colonna 1: Brand -->
                <div class="footer-section">
                    <div class="footer-brand">
                        <div class="brand-name"><span lang="en">InfuseMe</span></div>
                        <div class="motto-brand">Taste Tradition</div>
                    </div>
                </div>

                <!-- Colonna 2: Contatti e Lavora con Noi-->
                <div class="footer-section">
                    <h3>Contatti</h3>
                    <address>
                        <div class="contact">
                            <strong>Centralino:</strong> +39 000 111 abcd 
                        </div>
                        <div class="contact">
                            <strong>Customer Care:</strong> +39 111 222 efgh
                        </div>
                        <div class="contact">
                            <strong>Assistenza Clienti:</strong> assistenza@infuseme.com
                        </div>
                    </address>

                    <h3>Lavora con Noi</h3>
                    <div class="work-item">
                        <strong>Carriere:</strong> hr@infuseme.com
                    </div>
                    <div class="work-item">
                        <strong>Collaborazioni:</strong> procurement@infuseme.com
                    </div>
                </div>

                <!-- Colonna 3: Orari Sedi -->
                <div class="footer-section">
                    <h3>Orari Ufficio</h3>
                    <div class="hours">
                        <strong>Lunedì - Venerdì:</strong> 9:00 - 18:00
                    </div>
                    <div class="hours">
                        <strong>Sabato:</strong> 9:00 - 13:00
                    </div>
                    <div class="hours">
                        <strong>Domenica:</strong> Chiuso
                    </div>

                    <h3>Le Nostre Sedi</h3>
                    <div class="location-item">Val d'Ossola (Piemonte)</div>
                    <div class="location-item">Biella</div>
                </div>
            </div>

            <!-- Social Media Icons -->
            <div class="footer-social">
                <h3>Seguici sui social</h3>
                <div class="social-icons">
                    <!-- Stesso markup social di 404.php -->
                    <?php include $_SERVER['DOCUMENT_ROOT'] . '/social-icons.php'; ?>
                </div>
            </div>

            <div class="footer-bottom">
                <p>&copy; 2025 <span lang="en">InfuseMe</span>. Tutti i diritti riservati.</p>
            </div>
        </div>
    </footer>

    <!-- Pulsante Torna Su -->
    <button class="back-to-top" id="backToTop" aria-label="Torna all'inizio della pagina">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true">
            <path d="M18,15.5a1,1,0,0,1-.71-.29l-4.58-4.59a1,1,0,0,0-1.42,0L6.71,15.21a1,1,0,0,1-1.42-1.42L9.88,9.21a3.06,3.06,0,0,1,4.24,0l4.59,4.58a1,1,0,0,1,0,1.42A1,1,0,0,1,18,15.5Z"/>
        </svg>
    </button>

    <!-- Scripts -->
    <script src="javaScript/script.js"></script>
</body>
</html>