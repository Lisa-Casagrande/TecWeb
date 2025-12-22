const dettagli_form = {
    "nome": [/^[A-Za-z\sÀ-ÿ'’]{2,50}$/, "Errore: Nome non valido."],
    "cognome": [/^[A-Za-z\sÀ-ÿ'’]{2,50}$/, "Errore: Cognome non valido."],
    "data-nascita": [/^\d{4}-\d{2}-\d{2}$/, "Errore: Devi essere maggiorenne."],
    "citta": [/^[A-Za-z\sÀ-ÿ'’]{2,50}$/, "Errore: Città non valida."],
    "indirizzo": [/^[A-Za-z0-9\sÀ-ÿ'’.,-]{5,100}$/, "Errore: Indirizzo non valido."],
    "email": [/^[a-zA-Z0-9._%+-]{2,}@[a-zA-Z0-9.-]{2,}\.[a-zA-Z]{2,}$/, "Errore: Email non valida."],
    "password": [/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&]).{8,}$/, "Errore: Password non sicura."],
    "conferma-password": [null, "Errore: Le password non coincidono."]
};

function validazioneCampo(input) {
    const config = dettagli_form[input.id];
    if (!config) return true;

    const valore = input.value.trim();
    const regex = config[0];
    
    // Trova lo span di errore basandosi sull'id dell'input (es. nome -> nome-error)
    // Se non lo trova per ID, prova a cercarlo nella classe del genitore
    let span = document.getElementById(`${input.id}-error`);
    if (!span && input.id === "conferma-password") span = document.getElementById("conf-error");

    let errore = "";

    if (input.id === "conferma-password") {
        const pass = document.getElementById("password").value;
        if (valore === "" || valore !== pass) errore = config[1];
    } 
    else if (input.id === "data-nascita") {
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
    else if (regex && !regex.test(valore)) {
        errore = config[1];
    }

    // Applicazione Feedback
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

function caricamento() {
    const form = document.getElementById("registrazione-form");
    if (!form) return;

    Object.keys(dettagli_form).forEach(id => {
        const input = document.getElementById(id);
        if (input) {
            input.addEventListener("input", () => validazioneCampo(input));
            input.addEventListener("blur", () => validazioneCampo(input));
        }
    });

    form.addEventListener("submit", (e) => {
        let isValido = true;
        Object.keys(dettagli_form).forEach(id => {
            const input = document.getElementById(id);
            if (input && !validazioneCampo(input)) isValido = false;
        });
        if (!isValido) e.preventDefault();
    });
}

document.addEventListener("DOMContentLoaded", caricamento);