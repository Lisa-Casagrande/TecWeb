<?php
// Gestione sessioni
if (session_status() === PHP_SESSION_NONE) { 
    session_start(); 
}

// Se qualcuno è già loggato → logout automatico
if (isset($_SESSION['id_utente'])) {
    session_unset();
    session_destroy();
}

// Recupera eventuali errori e valori precedenti
$errors = $_SESSION['errors'] ?? [];
$old = $_SESSION['old'] ?? [];
unset($_SESSION['errors'], $_SESSION['old']);

// Funzione helper per decidere la classe CSS iniziale
function getClasse($campo, $errors, $old) {
    if (isset($errors[$campo])) return 'input-error';
    // Solo se il campo ha un valore **non vuoto e non è password** diventa valido
    if (isset($old[$campo]) && !empty($old[$campo]) && $campo !== 'reg_password' && $campo !== 'reg_conf') return 'input-valid';
    return '';
}

// Calcola la data massima per essere maggiorenne (18 anni)
$maxDataNascita = date('Y-m-d', strtotime('-18 years'));

// Prepara le sostituzioni per i placeholder nel template
$sostituzioni = [
    // Errore generale
    '[erroreGenerale]' => $errors['generale'] ?? '',

    // Errori specifici per ogni campo
    '[erroreNome]'       => $errors['reg_nome'] ?? '',
    '[erroreCognome]'    => $errors['reg_cognome'] ?? '',
    '[erroreNascita]'    => $errors['reg_data-nascita'] ?? '',
    '[erroreCitta]'      => $errors['reg_citta'] ?? '',
    '[erroreIndirizzo]'  => $errors['reg_indirizzo'] ?? '',
    '[erroreCap]'        => $errors['reg_cap'] ?? '',
    '[erroreEmail]'      => $errors['reg_email'] ?? '',
    '[errorePassword]'   => $errors['reg_password'] ?? '',
    '[erroreConferma]'   => $errors['reg_conf'] ?? '',

    // Valori precedenti (per ripopolare i campi)
    '[valoreNome]'       => htmlspecialchars($old['reg_nome'] ?? ''),
    '[valoreCognome]'    => htmlspecialchars($old['reg_cognome'] ?? ''),
    '[valoreData]'       => htmlspecialchars($old['reg_data-nascita'] ?? ''),
    '[valoreCitta]'      => htmlspecialchars($old['reg_citta'] ?? ''),
    '[valoreIndirizzo]'  => htmlspecialchars($old['reg_indirizzo'] ?? ''),
    '[valoreCap]'        => htmlspecialchars($old['reg_cap'] ?? ''),
    '[valoreEmail]'      => htmlspecialchars($old['reg_email'] ?? ''),
    '[valorePassword]'  => htmlspecialchars($old['reg_password'] ?? ''),
    
    // Classi CSS per input
    '[classeNome]'       => getClasse('reg_nome', $errors, $old),
    '[classeCognome]'    => getClasse('reg_cognome', $errors, $old),
    '[classeData]'       => getClasse('reg_data-nascita', $errors, $old),
    '[classeCitta]'      => getClasse('reg_citta', $errors, $old),
    '[classeIndirizzo]'  => getClasse('reg_indirizzo', $errors, $old),
    '[classeCap]'        => getClasse('reg_cap', $errors, $old),
    '[classeEmail]'      => getClasse('reg_email', $errors, $old),
    '[classePassword]'   => getClasse('reg_password', $errors, $old),
    '[classeConferma]'   => getClasse('reg_conf', $errors, $old),

    // Data massima per la nascita
    '[maxDataNascita]'   => $maxDataNascita
];

ob_start();
require 'navbar.php';
$navbar = ob_get_clean();
?>
<!DOCTYPE html>
<html lang="it" xml:lang="it" xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0" />
    <title>Registrati - InfuseMe</title>
    <meta name="description" content="Registrati al sito InfuseMe per acquistare i nostri prodotti artigianali" />
    <meta name="keywords" content="registrazione, InfuseMe, tè, infusi, tisane, biologico, artigianale, Val d'Ossola, blend, acquisto" />
    <link rel="stylesheet" href="style.css" type="text/css" />
</head>

<body>

    <!-- Skip link per accessibilità -->
    <a href="#main-content" class="skip-link">Salta al contenuto principale</a>

   <?php echo $navbar; ?>

  <main id="main-content">
    <section id="login-section">
        <div class="login-container">
            <h1>Crea il tuo <span lang="en">account</span></h1>
            <div class="form-links">
                <p>Hai già un <span lang="en">account</span>? <a href="login.php" id="torna-login">Accedi</a></p>
            </div>

            <!-- Errore generale lato server -->
            <div class="display-error" role="alert"><?php echo $sostituzioni['[erroreGenerale]']; ?></div>

            <form id="registrazione-form" action="php/elabora_registrazione.php" method="post" novalidate>
                <fieldset class="form-data">
                    <legend class="sr-only">Inserisci i dati per registrarsi</legend>

                    <!-- DATI ANAGRAFICI -->
                    <fieldset>
                        <legend class="sr-only">Dati anagrafici</legend>

                        <!-- NOME -->
                        <div class="form-group">
                            <label for="reg_nome">Nome *:</label>
                            <input type="text" id="reg_nome" name="reg_nome" autocomplete="given-name" required aria-required="true" aria-describedby="reg_nome-help reg_nome-error" value="<?php echo $sostituzioni['[valoreNome]']; ?>" class="<?php echo $sostituzioni['[classeNome]']; ?>">
                            <span id="reg_nome-error" class="errorSuggestion" role="alert"><?php echo $sostituzioni['[erroreNome]']; ?></span>
                            <small id="reg_nome-help" class="form-hint">Inserisci il tuo nome, minimo 2 caratteri, senza numeri o caratteri speciali.</small>
                        </div>

                        <!-- COGNOME -->
                        <div class="form-group">
                            <label for="reg_cognome">Cognome *:</label>
                            <input type="text" id="reg_cognome" name="reg_cognome" autocomplete="family-name" required aria-required="true" aria-describedby="reg_cognome-help reg_cognome-error" value="<?php echo $sostituzioni['[valoreCognome]']; ?>" class="<?php echo $sostituzioni['[classeCognome]']; ?>">
                            <span id="reg_cognome-error" class="errorSuggestion" role="alert"><?php echo $sostituzioni['[erroreCognome]']; ?></span>
                            <small id="reg_cognome-help" class="form-hint">Inserisci il tuo cognome, minimo 2 caratteri, senza numeri o caratteri speciali.</small>
                        </div>

                        <!-- DATA DI NASCITA -->
                        <div class="form-group">
                            <label for="reg_data-nascita">Data di nascita *:</label>
                            <input type="date" id="reg_data-nascita" name="reg_data-nascita" required aria-required="true" aria-describedby="reg_data-nascita-help reg_data-nascita-error" value="<?php echo $sostituzioni['[valoreData]']; ?>" max="<?php echo $sostituzioni['[maxDataNascita]']; ?>" class="<?php echo $sostituzioni['[classeData]']; ?>">
                            <span id="reg_data-nascita-error" class="errorSuggestion" role="alert"><?php echo $sostituzioni['[erroreNascita]']; ?></span>
                            <small id="reg_data-nascita-help" class="form-hint">La vendita è dedicata esclusivamente ai maggiorenni. In caso contrario è necessaria la supervisione di un adulto.</small>
                        </div>
                    </fieldset>

                    <!-- INDIRIZZO -->
                    <fieldset>
                        <legend class="sr-only">Indirizzo di spedizione</legend>

                        <!-- CITTÀ -->
                        <div class="form-group">
                            <label for="reg_citta">Città *:</label>
                            <input type="text" id="reg_citta" name="reg_citta" required aria-required="true" aria-describedby="reg_citta-help reg_citta-error" value="<?php echo $sostituzioni['[valoreCitta]']; ?>" class="<?php echo $sostituzioni['[classeCitta]']; ?>">
                            <span id="reg_citta-error" class="errorSuggestion" role="alert"><?php echo $sostituzioni['[erroreCitta]']; ?></span>
                            <small id="reg_citta-help" class="form-hint">Inserisci la tua città (<abbr title="Esempio">es.</abbr> Roma). Usata solo per spedizione.</small>
                        </div>

                        <!-- INDIRIZZO -->
                        <div class="form-group">
                            <label for="reg_indirizzo">Indirizzo *:</label>
                            <input type="text" id="reg_indirizzo" name="reg_indirizzo" required aria-required="true" aria-describedby="reg_indirizzo-help reg_indirizzo-error" value="<?php echo $sostituzioni['[valoreIndirizzo]']; ?>" class="<?php echo $sostituzioni['[classeIndirizzo]']; ?>">
                            <span id="reg_indirizzo-error" class="errorSuggestion" role="alert"><?php echo $sostituzioni['[erroreIndirizzo]']; ?></span>
                            <small id="reg_indirizzo-help" class="form-hint">Inserisci il tuo indirizzo (<abbr title="Esempio">es.</abbr> Via Turchese 18). Usato solo per spedizione.</small>
                        </div>

                        <!-- CAP -->
                        <div class="form-group">
                            <label for="reg_cap"><abbr title="Codice di Avviamento Postale"> CAP </abbr>*:</label>
                            <input type="text" id="reg_cap" name="reg_cap" maxlength="5" inputmode="numeric" pattern="\d{5}" required aria-required="true" aria-describedby="reg_cap-help reg_cap-error" value="<?php echo $sostituzioni['[valoreCap]']; ?>" class="<?php echo $sostituzioni['[classeCap]']; ?>">
                            <span id="reg_cap-error" class="errorSuggestion" role="alert"><?php echo $sostituzioni['[erroreCap]']; ?></span>
                            <small id="reg_cap-help" class="form-hint">
                            Inserisci il <abbr title="Codice di Avviamento Postale">CAP</abbr> a 5 cifre (<abbr title="Esempio">es.</abbr> 20121).</small>
                        </div>
                    </fieldset>

                    <!-- CREDENZIALI -->
                    <fieldset>
                        <legend class="sr-only">Credenziali di accesso</legend>

                       <!-- EMAIL -->
                        <div class="form-group">
                            <label for="reg_email">Email *:</label>
                            <input type="email"  id="reg_email" name="reg_email" required aria-describedby="reg_email-help reg_email-error" value="<?php echo $sostituzioni['[valoreEmail]']; ?>" class="<?php echo $sostituzioni['[classeEmail]']; ?>">
                            <span id="reg_email-error" class="errorSuggestion" role="alert"><?php echo $sostituzioni['[erroreEmail]']; ?></span> 
                            <small id="reg_email-help" class="form-hint">
                            Inserisci un indirizzo email valido (<abbr title="Esempio">es.</abbr> giulia.bianchi@esempio.it).</small>
                        </div>
                        <!-- PASSWORD -->
                        <div class="form-group">
                            <label for="reg_password">Password *:</label>
                            <input type="password" id="reg_password" name="reg_password" required aria-describedby="reg_password-help reg_password-error" class="<?php echo $sostituzioni['[classePassword]']; ?>" >
                            <!-- Mostra l'errore lato server -->
                            <span id="reg_password-error" class="errorSuggestion" role="alert"><?php echo $sostituzioni['[errorePassword]']; ?></span>
                            <small id="reg_password-help" class="form-hint">La password deve contenere:
                                <ul class="password-rules">
                                    <li>Almeno 8 caratteri</li>
                                    <li>Almeno una lettera maiuscola (A-Z)</li>
                                    <li>Almeno una lettera minuscola (a-z)</li>
                                    <li>Almeno un numero (0-9)</li>
                                    <li>Almeno un carattere speciale (@, $, !, %, *, ?, &amp;)</li>
                                </ul>
                            </small>
                        </div>

                        <!-- CONFERMA PASSWORD -->
                        <div class="form-group">
                            <label for="reg_conferma-password">Conferma Password *:</label>
                            <input type="password" id="reg_conferma-password" name="reg_conferma-password"  required aria-describedby="reg_conf-help reg_conf-error" class="<?php echo $sostituzioni['[classeConferma]']; ?>">
                            <!-- Mostra l'errore lato server -->
                            <span id="reg_conf-error" class="errorSuggestion" role="alert"><?php echo $sostituzioni['[erroreConferma]']; ?></span>
                            <small id="reg_conf-help" class="form-hint">La password deve corrispondere a quella inserita precedentemente.</small>
                        </div>
                    </fieldset>

                    <!-- SUBMIT -->
                    <div class="form-group">
                        <button type="submit" class="btn-login">Registrati</button>
                        <p id="reg_success-info" class="form-hint" aria-live="polite">
                          In caso di esito positivo, sarà effettuato il reindirizzamento al login!
                        </p>
                    </div>
                </fieldset>
            </form>
        </div>
    </section>
</main>
    <!-- Footer -->
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
                            <path d="m14.502,11.986c0,1.431-1.16,2.591-2.591,2.591s-2.59-1.16-2.59-2.591,1.16-2.591,2.59-2.591,2.591,1.16,2.591,2.591h0Zm0,0"/>
                            <path d="m12,0h0C5.373,0,0,5.373,0,12h0c0,6.627,5.373,12,12,12h0c6.627,0,12-5.373,12-12h0C24,5.373,18.627,0,12,0Zm7.637,15.19c-.037.827-.169,1.392-.361,1.886-.199.511-.465.945-.897,1.377-.432.432-.866.698-1.376.896-.494.192-1.06.323-1.887.361-.829.038-1.094.047-3.205.047s-2.375-.009-3.204-.047c-.827-.038-1.392-.169-1.887-.361-.511-.198-.944-.465-1.377-.896-.432-.432-.698-.866-.897-1.377-.192-.494-.323-1.059-.361-1.886-.038-.829-.047-1.094-.047-3.205s.009-2.375.047-3.204c.038-.827.169-1.392.361-1.887.199-.511.465-.944.897-1.376s.866-.698,1.377-.897c.494-.192,1.06-.323,1.887-.361.829-.038,1.094-.047,3.204-.047s2.376.009,3.205.047c.827.037,1.392.169,1.887.361.511.198.944.465,1.376.897.432.432.698.866.897,1.376.192.494.323,1.06.361,1.887.038.829.047,1.093.047,3.204s-.009,2.375-.047,3.205h0Zm-1.666-7.788c-.141-.363-.309-.622-.582-.894-.272-.272-.531-.441-.894-.582-.274-.106-.685-.233-1.443-.267-.82-.038-1.066-.045-3.141-.045s-2.321.008-3.141.045c-.757.034-1.169.161-1.443.267-.363.141-.622.309-.894.582-.272.272-.441.531-.582.894-.106.274-.233.685-.267,1.443-.038.819-.045,1.065-.045,3.141s.008,2.321.045,3.141c.035.757.161,1.169.267,1.443.141.363.309.622.582.894.272.272.531.44.894.581.274.107.685.233,1.443.268.819.038,1.065.045,3.141.045s2.322-.008,3.141-.045c.758-.035,1.169-.161,1.443-.268.363-.141.622-.309.894-.581s.441-.531.582-.894c.106-.274.233-.685.267-1.443.038-.82.046-1.066.046-3.141s-.008-2.321-.046-3.141c-.035-.758-.161-1.169-.267-1.443h0Zm-6.059,8.574c-2.204,0-3.991-1.787-3.991-3.991s1.787-3.991,3.991-3.991,3.991,1.787,3.991,3.991-1.787,3.991-3.991,3.991h0Zm4.149-7.207c-.515,0-.933-.417-.933-.932s.417-.933.933-.933.933.418.933.933-.418.932-.933.932h0Zm0,0"/>
                        </svg>
                    </span>

                    <!-- Facebook -->
                    <span class="social-icon" aria-label="Facebook" title="Seguici su Facebook">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M24,12.073c0,5.989-4.394,10.954-10.13,11.855v-8.363h2.789l0.531-3.46H13.87V9.86c0-0.947,0.464-1.869,1.95-1.869h1.509V5.045c0,0-1.37-0.234-2.679-0.234c-2.734,0-4.52,1.657-4.52,4.656v2.637H7.091v3.46h3.039v8.363C4.395,23.025,0,18.061,0,12.073c0-6.627,5.373-12,12-12S24,5.445,24,12.073z"/>
                        </svg>
                    </span>

                    <!-- TikTok -->
                    <span class="social-icon" aria-label="TikTok" title="Seguici su TikTok">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true">
                            <path d="m12,0C5.373,0,0,5.373,0,12s5.373,12,12,12,12-5.373,12-12S18.627,0,12,0h0Zm7.439,10.483c-1.52,0-2.93-.486-4.081-1.312v5.961c0,2.977-2.422,5.399-5.399,5.399-1.151,0-2.217-.363-3.094-.978-1.393-.978-2.305-2.594-2.305-4.421,0-2.977,2.422-5.399,5.399-5.399.247,0,.489.02,.727.053v2.994c-.23-.072-.474-.114-.727-.114-1.36,0-2.466,1.106-2.466,2.466,0,.947.537,1.769,1.322,2.183.342.18.731.283,1.144.283,1.329,0,2.412-1.057,2.461-2.373l.005-11.756h2.933c0,.254.025.503.069.744.207,1.117.87,2.077,1.789,2.676.64.418,1.403.661,2.222.661v2.933Zm0,0"/>
                        </svg>
                    </span>

                    <!-- LinkedIn -->
                    <span class="social-icon" aria-label="LinkedIn" title="Seguici su LinkedIn">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true">
                            <path d="m20.65,18.172h-.174v-.353h.22c.114,0,.244.018.244.167,0,.171-.131.185-.29.185Z"/>
                            <path d="m20.639,17.366c-.473.008-.85.398-.843.871.008.473.398.851.871.843h0s.022,0,.022,0c.463-.01.83-.393.821-.856v-.014c-.008-.473-.398-.851-.871-.843h0Zm.31,1.378l-.285-.449-.004-.005h-.184v.454h-.149v-1.043h.398c.246,0,.367.095.367.294,0,.006,0,.012,0,.018-.001.147-.095.266-.289.266l.308.465h-.160Z"/>
                            <path d="m12,0h0C5.373,0,0,5.373,0,12h0c0,6.627,5.373,12,12,12h0c6.627,0,12-5.373,12-12h0C24,5.373,18.627,0,12,0Zm7.037,18.056c-.008.578-.483,1.042-1.062,1.034H5.76c-.577.006-1.051-.457-1.058-1.034V5.79c.007-.577.48-1.04,1.058-1.033h12.215c.578-.009,1.053.454,1.062,1.032v12.267Zm1.65,1.136c-.54.005-.982-.428-.987-.968-.005-.539.428-.981.968-.987h.019c.532.005.963.436.968.968.005.539-.428.981-.968.987Zm-6.32-9.232c-.823-.03-1.596.394-2.012,1.105h-.028v-.935h-2.039v6.84h2.124v-3.383c0-.893.169-1.756,1.276-1.756,1.09,0,1.104,1.021,1.104,1.814v3.326h2.124v-3.752c0-1.843-.396-3.258-2.549-3.258h0Zm-7.54,7.01h2.126v-6.84h-2.126v6.84Zm1.064-10.24c-.681,0-1.233.552-1.233,1.233,0,.681.552,1.232,1.233,1.232s1.233-.552,1.233-1.233c0-.681-.552-1.233-1.233-1.232Z"/>
                        </svg>
                    </span>

                    <!-- Pinterest -->
                    <span class="social-icon" aria-label="Pinterest" title="Seguici su Pinterest">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M12.01,0C5.388,0,0.02,5.368,0.02,11.99c0,5.082,3.158,9.424,7.618,11.171c-0.109-0.947-0.197-2.408,0.039-3.444c0.217-0.938,1.401-5.961,1.401-5.961s-0.355-0.72-0.355-1.776c0-1.668,0.967-2.911,2.171-2.911c1.026,0,1.52,0.77,1.52,1.688c0,1.026-0.651,2.566-0.997,3.997c-0.286,1.194,0.602,2.171,1.776,2.171c2.132,0,3.77-2.25,3.77-5.487c0-2.872-2.062-4.875-5.013-4.875c-3.414,0-5.418,2.556-5.418,5.201c0,1.026,0.395,2.132,0.888,2.734C7.52,14.615,7.53,14.724,7.5,14.842c-0.089,0.375-0.296,1.194-0.336,1.362c-0.049,0.217-0.178,0.266-0.405,0.158c-1.5-0.701-2.438-2.882-2.438-4.648c0-3.78,2.743-7.253,7.924-7.253c4.155,0,7.391,2.961,7.391,6.928c0,4.135-2.605,7.461-6.217,7.461c-1.214,0-2.359-0.632-2.743-1.382c0,0-0.602,2.289-0.75,2.852c-0.266,1.046-0.997,2.349-1.49,3.148C9.562,23.812,10.747,24,11.99,24,9.562,23.812,10.747,24,11.99,24c6.622,0,11.99-5.368,11.99-11.99C24,5.368,18.632,0,12.01,0z"/>
                        </svg>
                    </span>
                </div>
            </div>

            <div class="footer-bottom">
                <p>&copy; 2025 <span lang="en">InfuseMe</span>. Tutti i diritti riservati.</p>
            </div>

        </div> <!--fine class container-->
    </footer>

    <!-- Pulsante Torna Su (no title perchè c'è nel css)-->
   <button class="back-to-top" id="backToTop" aria-label="Torna all'inizio della pagina">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true">
            <path d="M18,15.5a1,1,0,0,1-.71-.29l-4.58-4.59a1,1,0,0,0-1.42,0L6.71,15.21a1,1,0,0,1-1.42-1.42L9.88,9.21a3.06,3.06,0,0,1,4.24,0l4.59,4.58a1,1,0,0,1,0,1.42A1,1,0,0,1,18,15.5Z"/>
        </svg>
    </button>

    <!--file js -->
    <script src="javaScript/script.js"></script>
    <script src="javaScript/registrazioneValidator.js"></script>

</body>
</html>