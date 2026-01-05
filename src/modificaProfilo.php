<?php
require_once 'php/verificaSessione.php';
require_once 'php/connessione.php';

$userId = userId(); // ottieni ID utente loggato

try {
    // Recupero dati utente
    $stmt = $pdo->prepare("
        SELECT nome, cognome, email, data_nascita, indirizzo, citta, cap, paese
        FROM utente
        WHERE id_utente = :id
        LIMIT 1
    ");
    $stmt->execute([':id' => $userId]);
    $utente = $stmt->fetch();

    if (!$utente) {
        die("Utente non trovato.");
    }

    // Se il form è stato inviato
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Pulizia e validazione minima
        $nome = trim($_POST['nome']);
        $cognome = trim($_POST['cognome']);
        $email = trim($_POST['email']);
        $data_nascita = trim($_POST['data_nascita']);
        $indirizzo = trim($_POST['indirizzo']);
        $citta = trim($_POST['citta']);
        $cap = trim($_POST['cap']);
        $paese = trim($_POST['paese']);

        if (empty($nome) || empty($cognome) || empty($email) || empty($data_nascita)) {
            $error = "Nome, cognome, email e data di nascita sono obbligatori.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = "Email non valida.";
        } else {
            // Aggiorna dati
            $stmt_update = $pdo->prepare("
                UPDATE utente
                SET nome = :nome,
                    cognome = :cognome,
                    email = :email,
                    data_nascita = :data_nascita,
                    indirizzo = :indirizzo,
                    citta = :citta,
                    cap = :cap,
                    paese = :paese
                WHERE id_utente = :id
            ");
            $stmt_update->execute([
                ':nome' => $nome,
                ':cognome' => $cognome,
                ':email' => $email,
                ':data_nascita' => $data_nascita,
                ':indirizzo' => $indirizzo,
                ':citta' => $citta,
                ':cap' => $cap,
                ':paese' => $paese,
                ':id' => $userId
            ]);

            $success = "Profilo aggiornato correttamente!";
            // Aggiorna i dati per mostrare il form con valori nuovi
            $utente = [
                'nome' => $nome,
                'cognome' => $cognome,
                'email' => $email,
                'data_nascita' => $data_nascita,
                'indirizzo' => $indirizzo,
                'citta' => $citta,
                'cap' => $cap,
                'paese' => $paese
            ];
        }
    }
} catch (PDOException $e) {
    $error = "Errore database: ";}
?>

<!DOCTYPE html>
<html lang="it" xml:lang="it" xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0" />
    <title>Modifica Profilo - InfuseMe</title>
     <meta name="description" content="Modifica il tuo account personale nel nostro negozio di tè, infusi e tisane di qualità" />
    <meta name="keywords" content="tè, infusi, tisane, modifca, account utente, profilo, ordini, preferenze" />
    <link rel="stylesheet" href="style.css" type="text/css" />
</head>
<body>
<a href="#main-content" class="skip-link">Salta al contenuto principale</a>
<?php include 'navbar.php'; ?>

<main id="content">
    <section id="area-account">
        <h1>Modifica il Mio Profilo</h1>

        <form action="" method="post" class="form-modifica-profilo">
            <label for="nome">Nome *</label>
            <input type="text" id="nome" name="nome" value="<?= htmlspecialchars($utente['nome']) ?>" required>

            <label for="cognome">Cognome *</label>
            <input type="text" id="cognome" name="cognome" value="<?= htmlspecialchars($utente['cognome']) ?>" required>

            <label for="email">Email *</label>
            <input type="email" id="email" name="email" value="<?= htmlspecialchars($utente['email']) ?>" required>

            <label for="data_nascita">Data di Nascita *</label>
            <input type="date" id="data_nascita" name="data_nascita" value="<?= htmlspecialchars($utente['data_nascita']) ?>" required>

            <label for="indirizzo">Indirizzo</label>
            <input type="text" id="indirizzo" name="indirizzo" value="<?= htmlspecialchars($utente['indirizzo']) ?>">

            <label for="citta">Città</label>
            <input type="text" id="citta" name="citta" value="<?= htmlspecialchars($utente['citta']) ?>">

            <label for="cap">CAP</label>
            <input type="text" id="cap" name="cap" value="<?= htmlspecialchars($utente['cap']) ?>">

            <label for="paese">Paese</label>
            <input type="text" id="paese" name="paese" value="<?= htmlspecialchars($utente['paese']) ?>">

            <div class="form-buttons">
                <button type="submit">Salva Modifiche</button>
                <a href="paginautente.php" class="button-secondary">Annulla</a>
            </div>
        </form>
    </section>
</main>

 <footer>
        <div class="container">
            <div class="footer-content">
                <!-- Colonna 1: Brand -->
                <div class="footer-section">
                    <div class="footer-brand">
                        <div class="brand-name"><span lang="en">InfuseMe</span></div>
                        <div class="motto-brand"><span lang="en">Taste Tradition</span></div>
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
                            <strong><span lang="en">Customer Care</span>:</strong> +39 111 222 efgh
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

                </div> <!--fine footer content-->


                <!-- Social Media Icons - solo icoe dei social-->
                <div class="footer-social">
                    <h3>Seguici sui social</h3>
                    <div class="social-icons">
                        <!-- Instagram -->
                        <span class="social-icon" aria-label="Instagram" title="Seguici su Instagram">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true">
                                <path
                                    d="m14.502,11.986c0,1.431-1.16,2.591-2.591,2.591s-2.59-1.16-2.59-2.591,1.16-2.591,2.59-2.591,2.591,1.16,2.591,2.591h0Zm0,0" />
                                <path
                                    d="m12,0h0C5.373,0,0,5.373,0,12h0c0,6.627,5.373,12,12,12h0c6.627,0,12-5.373,12-12h0C24,5.373,18.627,0,12,0Zm7.637,15.19c-.037.827-.169,1.392-.361,1.886-.199.511-.465.945-.897,1.377-.432.432-.866.698-1.376.896-.494.192-1.06.323-1.887.361-.829.038-1.094.047-3.205.047s-2.375-.009-3.204-.047c-.827-.038-1.392-.169-1.887-.361-.511-.198-.944-.465-1.377-.896-.432-.432-.698-.866-.897-1.377-.192-.494-.323-1.059-.361-1.886-.038-.829-.047-1.094-.047-3.205s.009-2.375.047-3.204c.038-.827.169-1.392.361-1.887.199-.511.465-.944.897-1.376s.866-.698,1.377-.897c.494-.192,1.06-.323,1.887-.361.829-.038,1.094-.047,3.204-.047s2.376.009,3.205.047c.827.037,1.392.169,1.887.361.511.198.944.465,1.376.897.432.432.698.866.897,1.376.192.494.323,1.06.361,1.887.038.829.047,1.093.047,3.204s-.009,2.375-.047,3.205h0Zm-1.666-7.788c-.141-.363-.309-.622-.582-.894-.272-.272-.531-.441-.894-.582-.274-.106-.685-.233-1.443-.267-.82-.038-1.066-.045-3.141-.045s-2.321.008-3.141.045c-.757.034-1.169.161-1.443.267-.363.141-.622.309-.894.582-.272.272-.441.531-.582.894-.106.274-.233.685-.267,1.443-.038.819-.045,1.065-.045,3.141s.008,2.321.045,3.141c.035.757.161,1.169.267,1.443.141.363.309.622.582.894.272.272.531.44.894.581.274.107.685.233,1.443.268.819.038,1.065.045,3.141.045s2.322-.008,3.141-.045c.758-.035,1.169-.161,1.443-.268.363-.141.622-.309.894-.581s.441-.531.582-.894c.106-.274.233-.685.267-1.443.038-.82.046-1.066.046-3.141s-.008-2.321-.046-3.141c-.035-.758-.161-1.169-.267-1.443h0Zm-6.059,8.574c-2.204,0-3.991-1.787-3.991-3.991s1.787-3.991,3.991-3.991,3.991,1.787,3.991,3.991-1.787,3.991-3.991,3.991h0Zm4.149-7.207c-.515,0-.933-.417-.933-.932s.417-.933.933-.933.933.418.933.933-.418.932-.933.932h0Zm0,0" />
                            </svg>
                        </span>

                        <!-- Facebook -->
                        <span class="social-icon" aria-label="Facebook" title="Seguici su Facebook">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true">
                                <path
                                    d="M24,12.073c0,5.989-4.394,10.954-10.13,11.855v-8.363h2.789l0.531-3.46H13.87V9.86c0-0.947,0.464-1.869,1.95-1.869h1.509V5.045c0,0-1.37-0.234-2.679-0.234c-2.734,0-4.52,1.657-4.52,4.656v2.637H7.091v3.46h3.039v8.363C4.395,23.025,0,18.061,0,12.073c0-6.627,5.373-12,12-12S24,5.445,24,12.073z" />
                            </svg>
                        </span>

                        <!-- TikTok -->
                        <span class="social-icon" aria-label="TikTok" title="Seguici su TikTok">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true">
                                <path
                                    d="m12,0C5.373,0,0,5.373,0,12s5.373,12,12,12,12-5.373,12-12S18.627,0,12,0h0Zm7.439,10.483c-1.52,0-2.93-.486-4.081-1.312v5.961c0,2.977-2.422,5.399-5.399,5.399-1.151,0-2.217-.363-3.094-.978-1.393-.978-2.305-2.594-2.305-4.421,0-2.977,2.422-5.399,5.399-5.399.247,0,.489.02.727.053v2.994c-.23-.072-.474-.114-.727-.114-1.36,0-2.466,1.106-2.466,2.466,0,.947.537,1.769,1.322,2.183.342.18.731.283,1.144.283,1.329,0,2.412-1.057,2.461-2.373l.005-11.756h2.933c0,.254.025.503.069.744.207,1.117.87,2.077,1.789,2.676.64.418,1.403.661,2.222.661v2.933Zm0,0" />
                            </svg>
                        </span>

                        <!-- LinkedIn -->
                        <span class="social-icon" aria-label="LinkedIn" title="Seguici su LinkedIn">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true">
                                <path
                                    d="m20.65,18.172h-.174v-.353h.22c.114,0,.244.018.244.167,0,.171-.131.185-.29.185Z" />
                                <path
                                    d="m20.639,17.366c-.473.008-.85.398-.843.871.008.473.398.851.871.843h0s.022,0,.022,0c.463-.01.83-.393.821-.856v-.014c-.008-.473-.398-.851-.871-.843h0Zm.31,1.378l-.285-.449-.004-.005h-.184v.454h-.149v-1.043h.398c.246,0,.367.095.367.294,0,.006,0,.012,0,.018-.001.147-.095.266-.289.266l.308.465h-.160Z" />
                                <path
                                    d="m12,0h0C5.373,0,0,5.373,0,12h0c0,6.627,5.373,12,12,12h0c6.627,0,12-5.373,12-12h0C24,5.373,18.627,0,12,0Zm7.037,18.056c-.008.578-.483,1.042-1.062,1.034H5.76c-.577.006-1.051-.457-1.058-1.034V5.79c.007-.577.48-1.04,1.058-1.033h12.215c.578-.009,1.053.454,1.062,1.032v12.267Zm1.65,1.136c-.54.005-.982-.428-.987-.968-.005-.539.428-.981.968-.987h.019c.532.005.963.436.968.968.005.539-.428.981-.968.987Zm-6.32-9.232c-.823-.03-1.596.394-2.012,1.105h-.028v-.935h-2.039v6.84h2.124v-3.383c0-.893.169-1.756,1.276-1.756,1.09,0,1.104,1.021,1.104,1.814v3.326h2.124v-3.752c0-1.843-.396-3.258-2.549-3.258h0Zm-7.54,7.01h2.126v-6.84h-2.126v6.84Zm1.064-10.24c-.681,0-1.233.552-1.233,1.233,0,.681.552,1.232,1.233,1.232s1.233-.552,1.233-1.233c0-.681-.552-1.233-1.233-1.232Z" />
                            </svg>
                        </span>

                        <!-- Pinterest -->
                        <span class="social-icon" aria-label="Pinterest" title="Seguici su Pinterest">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true">
                                <path
                                    d="M12.01,0C5.388,0,0.02,5.368,0.02,11.99c0,5.082,3.158,9.424,7.618,11.171c-0.109-0.947-0.197-2.408,0.039-3.444c0.217-0.938,1.401-5.961,1.401-5.961s-0.355-0.72-0.355-1.776c0-1.668,0.967-2.911,2.171-2.911c1.026,0,1.52,0.77,1.52,1.688c0,1.026-0.651,2.566-0.997,3.997c-0.286,1.194,0.602,2.171,1.776,2.171c2.132,0,3.77-2.25,3.77-5.487c0-2.872-2.062-4.875-5.013-4.875c-3.414,0-5.418,2.556-5.418,5.201c0,1.026,0.395,2.132,0.888,2.734C7.52,14.615,7.53,14.724,7.5,14.842c-0.089,0.375-0.296,1.194-0.336,1.362c-0.049,0.217-0.178,0.266-0.405,0.158c-1.5-0.701-2.438-2.882-2.438-4.648c0-3.78,2.743-7.253,7.924-7.253c4.155,0,7.391,2.961,7.391,6.928c0,4.135-2.605,7.461-6.217,7.461c-1.214,0-2.359-0.632-2.743-1.382c0,0-0.602,2.289-0.75,2.852c-0.266,1.046-0.997,2.349-1.49,3.148C9.562,23.812,10.747,24,11.99,24,9.562,23.812,10.747,24,11.99,24c6.622,0,11.99-5.368,11.99-11.99C24,5.368,18.632,0,12.01,0z" />
                            </svg>
                        </span>
                    </div>
                </div>

                <div class="footer-bottom">
                    <p>&copy; 2025 <span lang="en">InfuseMe</span>. Tutti i diritti riservati.</p>
                </div>

            </div> <!--fine class container-->
    </footer>

    <script src="javaScript/script.js"></script>

    
</body>
</html>
