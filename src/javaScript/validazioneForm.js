/** ARRAY ASSOCIATIVO PER CAMPI DELLA FORM
* chiave: nome dell'input che cerco
* [0]: Prima indicazione per la compilazione dell'input
* [1]: espressione regolare da controllare
* [2]: Hint nel caso in cui input fornito sia sbagliato
*/

var dettagli_form = {
    "nome": [
        "Inserisci il nome del prodotto",
        /^[a-zA-Z0-9\s\']{2,100}$/,
        "Il nome deve contenere almeno 2 caratteri (lettere, numeri, spazi)."
    ],
    "descrizione": [
        "Descrivi il sapore e le caratteristiche",
        /^[\s\S]{10,2000}$/, /* \s\S accetta tutto inclusi a capo */
        "La descrizione deve essere lunga almeno 10 caratteri."
    ],
    "categoria": [
        "Seleziona la cateoria tra quelle proposte",
        /^(tè_verde|tè_nero|tè_bianco|tè_giallo|tè_oolong|tisana|infuso|altro)$/,
        "Selezionare una categoria valida."
    ],
    "prezzo": [
        "Prezzo in Euro (es. 10.50)",
        /^\d+(\.\d{1,2})?$/,
        "Inserire un prezzo valido (usa il punto per i decimali, es. 10.50)."
    ],
    "disponibilita": [
        "Quantità in magazzino",
        /^\d+$/,
        "Inserire un numero intero positivo."
    ],
    /*nel db il campo id_base può essere NULL (anche grammi e img_path)
    Se l'utente lascia la tendina su "-- Seleziona --", il valore inviato è vuoto (va bene perchè accetta NULL)*/
    "id_base": [
        "Base del blend",
        /^(\d*)$/, /*accetta un numero intero o una stringa vuota*/
        "Selezionare una base valida."
    ],
    "grammi": [
        "Peso della confezione (opzionale)",
        /^\d*$/,
        "Inserire un numero intero positivo (grammi)."
    ],
    "img_path": [
        "Foto del prodotto (opzionale)",
        /^$|(\.(jpg|jpeg|webp)$)/i,
        "Il file deve essere un'immagine (.jpg, .jpeg, .webp)."
    ]
};

function caricamento() {
    /*per ogni key nell'array associativo: */
    for (var key in dettagli_form) {
        var input = document.getElementById(key);
        if (input) { //controllo in più per campi obbligatori
            messaggio(input, 0); /*scrive suggerimento iniziale*/
            input.onblur = function() { /*evento che funziona sia con mouse, che con tab che con touch*/
                validazioneCampo(this);
            };
        }
    }

    var formNode = document.getElementById("form");
    if (formNode) {
        formNode.onsubmit = function() { /*per validare la form prima di inviare risposte*/
            return validazioneForm();
        };
    }
}


//validazione del singolo campo della form
function validazioneCampo(input) {
    //ignora campi del form che non sono presenti nell'array
    if (!dettagli_form[input.id]) return true;

    var regex = dettagli_form[input.id][1]; /*espressione regolare*/
    var text = input.value;

    //recupera nome reale dll'immagine
    if (input.type === "file") {
        if (input.files.length > 0) {
            text = input.files[0].name;
        } else {
            text = ""; /*se vuoto, la regex lo accetta comunque (no obbligatorio)*/
        }
    }

    /*controllo quanti figli ha il padre per evitare che il messaggio di errore compaia tre volte (=3 campi array)*/
    var p = input.parentNode;
    if(p.children[2]){ /*se c'é giá testo errore rimuovo (basta mettere anche l'istruzione senza if)*/
		p.removeChild(p.children[2]);
	}

    if (text.search(regex) !== 0) {
        messaggio(input, 1);
        return false;
    }
    return true;
}

//validazione dell'intera form (se admin clicca su salva senza mettere i dati obbligatori)
function validazioneForm() {
    var tuttoCorretto = true;
    
    for (var key in dettagli_form) {
        var input = document.getElementById(key);
        tuttoCorretto = validazioneCampo(input) && tuttoCorretto; /*prima mettere validazioneCampo sempre*/
    }
    return tuttoCorretto;
}

function messaggio(input, mode) {
    /* mode = 0: suggerimento, 
    mode = 1: errore */
    var node;
    var p = input.parentNode;

    if (mode) { /*mode=1 (true) = modalità errore*/
        node = document.createElement("strong");
        node.className = "errorSuggestion"; /*classi definite nel css*/
        node.appendChild(document.createTextNode(dettagli_form[input.id][2]));
    
    } else { /*input=0 = Modalità Suggerimento (suggerimento che compare nei campi all'inizio*/

            node = document.createElement("span");
            node.className = "default-text";
            node.appendChild(document.createTextNode(dettagli_form[input.id][0]));
    }

    //vale per entrambi i blocchi
    if (node) {
        p.appendChild(node);
    }
}

/* Usa addEventListener perché ci sono altri file JS attivi nella pagina */
window.addEventListener("load", caricamento);


/* da definire nel css

classe per errori della form aggiungiProdotto: 
.errorSuggestion {
    node.style.color = "#d9534f"; // Rosso
    node.style.display = "block";
    node.style.fontSize = "0.8em";
    node.style.marginTop = "5px";

    input.style.border = "2px solid #d9534f";
}


classe per i suggerimenti che compaiono nella form aggiungiProdotto
.default-text{
node.style.display = "block";
    node.style.fontSize = "0.8em";
    node.style.color = "#666";
}


*/

