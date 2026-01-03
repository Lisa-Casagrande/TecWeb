const dettagli_form_reg = {
    "reg_nome": [
        /^[A-Za-z\sÀ-ÿ'’]{2,50}$/,
        "Errore: Nome non valido. Usa solo lettere, minimo 2 caratteri."
    ],
    "reg_cognome": [
        /^[A-Za-z\sÀ-ÿ'’]{2,50}$/,
        "Errore: Cognome non valido. Usa solo lettere, minimo 2 caratteri."
    ],
    "reg_data-nascita": [
        /^\d{4}-\d{2}-\d{2}$/,
        "Errore: Devi essere maggiorenne."
    ],
    "reg_citta": [
        /^[A-Za-z\sÀ-ÿ'’]{2,50}$/,
        "Errore: Città non valida."
    ],
    "reg_indirizzo": [
        /^[A-Za-z0-9\sÀ-ÿ'’.,-]{5,100}$/,
        "Errore: Indirizzo non valido."
    ],
    "reg_email": [
        /^(?!.*[.\-_]{2})[A-Za-z0-9][A-Za-z0-9._%+-]{1,62}[A-Za-z0-9]@[A-Za-z0-9][A-Za-z0-9.-]{1,62}[A-Za-z0-9]\.[A-Za-z]{2,}$/,
        "Errore: Email non valida. Controlla formato, caratteri e dominio."
    ],
    "reg_password": [
        /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/,
        "Errore: Password non sicura. Deve contenere almeno 8 caratteri, una maiuscola, una minuscola, un numero e un carattere speciale."
    ],
    "reg_conferma-password": [
        null,
        "Errore: Le password non coincidono."
    ]
};

// Funzione di validazione singolo campo
function validazioneCampo(input) {
    const config = dettagli_form_reg[input.id];
    if (!config) return true;

    const valore = input.value.trim();
    const regex = config[0];

    // Span per errore
    let span = document.getElementById(`${input.id}-error`);
    if (!span && input.id === "reg_conferma-password") {
        span = document.getElementById("reg_conf-error");
    }

    let errore = "";

    // Controllo conferma password
    if (input.id === "reg_conferma-password") {
        const pass = document.getElementById("reg_password").value;
        if (valore === "" || valore !== pass) errore = config[1];
    } 
    // Controllo data nascita
    else if (input.id === "reg_data-nascita") {
        if (valore === "") {
            errore = "Errore: Data obbligatoria.";
        } else {
            const oggi = new Date();
            const nascita = new Date(valore);
            let eta = oggi.getFullYear() - nascita.getFullYear();
            const m = oggi.getMonth() - nascita.getMonth();
            if (m < 0 || (m === 0 && oggi.getDate() < nascita.getDate())) eta--;
            if (eta < 18) errore = config[1];
        }
    } 
    // Controllo regex generica
    else if (regex && !regex.test(valore)) {
        errore = config[1];
    }

    // Feedback immediato
    if (errore) {
        if (span) span.textContent = errore;
        input.classList.add("input-error");
        input.classList.remove("input-valid");
    } else {
        if (span) span.textContent = "";
        if (valore !== "") {
            input.classList.remove("input-error");
            input.classList.add("input-valid");
        } else {
            input.classList.remove("input-error", "input-valid");
        }
    }

    return errore === "";
}

// Funzione caricamento eventi
function caricamento() {
    const form = document.getElementById("registrazione-form");
    if (!form) return;

    Object.keys(dettagli_form_reg).forEach(id => {
        const input = document.getElementById(id);
        if (input) {
            input.addEventListener("input", () => validazioneCampo(input));
            input.addEventListener("blur", () => validazioneCampo(input));
        }
    });

    form.addEventListener("submit", (e) => {
        let isValido = true;
        let firstInvalid = null;

        Object.keys(dettagli_form_reg).forEach(id => {
            const input = document.getElementById(id);
            if (input && !validazioneCampo(input)) {
                isValido = false;
                if (!firstInvalid) firstInvalid = input;
            }
        });

        // Focus sul primo errore
        if (firstInvalid) firstInvalid.focus();

        if (!isValido) e.preventDefault(); // Blocca submit solo se ci sono errori
    });
}

document.addEventListener("DOMContentLoaded", caricamento);