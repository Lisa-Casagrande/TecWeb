// Configurazione campi e regole di validazione
const dettagli_form_reg = {
  "reg_nome": [/^[A-Za-z\sÀ-ÿ'’]{2,50}$/, "Errore: Nome non valido."],
  "reg_cognome": [/^[A-Za-z\sÀ-ÿ'’]{2,50}$/, "Errore: Cognome non valido."],
  "reg_data-nascita": [null, "Errore: Devi essere maggiorenne."],
  "reg_citta": [/^[A-Za-z\sÀ-ÿ'’]{2,50}$/, "Errore: Città non valida."],
  "reg_indirizzo": [/^[A-Za-z0-9\sÀ-ÿ'’.,-]{5,100}$/, "Errore: Indirizzo non valido."],
  "reg_cap": [/^\d{5}$/, "Errore: Il CAP deve contenere esattamente 5 numeri."],
  "reg_email": [/^(?!.*[.\-_]{2})[A-Za-z0-9][A-Za-z0-9._%+-]{1,62}[A-Za-z0-9]@[A-Za-z0-9][A-Za-z0-9.-]{1,62}[A-Za-z0-9]\.[A-Za-z]{2,}$/, "Errore: Email non valida."],
  "reg_password": [/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/, "Errore: Password non sicura."],
  "reg_conferma-password": [null, "Errore: Le password non coincidono."]
};

// Funzione di validazione singolo campo
function validazioneCampo(input) {
  const config = dettagli_form_reg[input.id];
  if (!config) return true;

  const valore = input.value.trim();
  const regex = config[0];

  // Span di errore
  let span = document.getElementById(`${input.id}-error`);
  if (!span && input.id === "reg_conferma-password") span = document.getElementById("reg_conf-error");

  let errore = "";

  // Controllo conferma password
  if (input.id === "reg_conferma-password") {
    const pass = document.getElementById("reg_password")?.value || "";
    if (valore === "" || valore !== pass) errore = config[1];
  }
  // Controllo data nascita
  else if (input.id === "reg_data-nascita") {
    if (valore === "") errore = "Errore: Data obbligatoria.";
    else {
      const oggi = new Date();
      const nascita = new Date(valore);
      let eta = oggi.getFullYear() - nascita.getFullYear();
      const m = oggi.getMonth() - nascita.getMonth();
      if (m < 0 || (m === 0 && oggi.getDate() < nascita.getDate())) eta--;
      if (eta < 18) errore = config[1];
    }
  }
  // Validazione regex
  else if (regex && !regex.test(valore)) {
    errore = config[1];
  }

  // Mostra errore e classi CSS
  if (span) span.textContent = errore;
  input.classList.toggle("input-error", !!errore);
  input.classList.toggle("input-valid", !errore && valore !== "");

  return errore === "";
}

// Funzione di inizializzazione
function inizializzaValidator() {
  const form = document.getElementById("registrazione-form") || document.getElementById("modifica-form");
  if (!form) return;

  // Aggiungi eventi di validazione ai campi
  Object.keys(dettagli_form_reg).forEach(id => {
    const input = document.getElementById(id);
    if (!input) return;
    input.addEventListener("input", () => validazioneCampo(input));
    input.addEventListener("blur", () => validazioneCampo(input));
  });

  // Validazione form al submit
  form.addEventListener("submit", e => {
    let isValido = true;
    let firstInvalid = null;

    Object.keys(dettagli_form_reg).forEach(id => {
      const input = document.getElementById(id);
      if (input && !validazioneCampo(input)) {
        isValido = false;
        if (!firstInvalid) firstInvalid = input;
      }
    });

    if (!isValido) {
      e.preventDefault();
      if (firstInvalid) firstInvalid.focus();
    }
  });
}

// Avvio quando il DOM è pronto
document.addEventListener("DOMContentLoaded", inizializzaValidator);
