/**
 * FILE UNICO JAVASCRIPT
 * tenendo tutto in un unico file il caricamento è più veloce
 -> da aggiornare tutti i file html e php alla fine includendo solo questo file e non tutti gli altri 
*/

/* ==========================================================================
   SEZIONE 1: GESTIONE TEMA (ex tema.js)
   Gestione tema chiaro/scuro per InfuseMe
   Implementazione accessibile e user-friendly
   Persistenza tema
   ========================================================================== */

// Configurazione
const ThemeManager = {
    // Elementi DOM
    elements: {
        body: null,
        themeToggle: null,
        sunIcon: null,
        moonIcon: null
    },

    // Stati
    states: {
        isDark: false,
        userPreference: null
    },

    // Inizializzazione
    init() {
        console.log('ThemeManager inizializzato');
        
        // Riferimenti agli elementi DOM
        this.elements.body = document.body;
        this.elements.themeToggle = document.querySelector('.theme-toggle');
        
        // **IMPORTANTE:** Cerca in tutta la pagina, non solo nel header
        this.elements.sunIcon = document.querySelector('.sun-icon');
        this.elements.moonIcon = document.querySelector('.moon-icon');

        // **APPLICA SUBITO** il tema all'avvio
        this.loadAndApplyTheme();

        // Aggiungi event listeners
        this.addEventListeners();

        // Aggiorna stato iniziale
        this.updateUI();
    },

    // Carica e applica il tema dalle preferenze
    loadAndApplyTheme() {
        console.log('Caricamento tema in corso...');
        
        // 1. Controlla preferenza utente salvata
        const savedTheme = localStorage.getItem('infuseme-theme');
        console.log('Tema salvato in localStorage:', savedTheme);
        
        // 2. Controlla preferenza sistema
        const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        console.log('Sistema preferisce scuro:', systemPrefersDark);
        
        // 3. Logica di priorità
        if (savedTheme === 'light' || savedTheme === 'dark') {
            // Utente ha scelto esplicitamente
            this.states.userPreference = savedTheme;
            this.states.isDark = savedTheme === 'dark';
            console.log('Usando preferenza utente:', savedTheme);
        } else {
            // Usa preferenza sistema (o default chiaro)
            this.states.userPreference = 'system';
            this.states.isDark = systemPrefersDark;
            console.log('Usando preferenza sistema:', systemPrefersDark ? 'dark' : 'light');
        }
        
        // 4. APPLICA IMMEDIATAMENTE il tema al DOM
        this.applyTheme();
        
        console.log(`Tema applicato: ${this.states.isDark ? 'scuro' : 'chiaro'}`);
    },

    // Applica il tema al DOM
    applyTheme() {
        console.log('Applicazione tema al DOM...');
        
        if (this.states.isDark) {
            // Tema scuro
            this.elements.body.classList.add('dark-theme');
            this.elements.body.classList.remove('light-theme');
            document.documentElement.setAttribute('data-theme', 'dark');
            
            // **AGGIUNTO:** Imposta anche un attributo custom per CSS
            document.documentElement.classList.add('dark-theme');
            document.documentElement.classList.remove('light-theme');
            
            console.log('Tema scuro applicato');
        } else {
            // Tema chiaro
            this.elements.body.classList.add('light-theme');
            this.elements.body.classList.remove('dark-theme');
            document.documentElement.setAttribute('data-theme', 'light');
            
            // **AGGIUNTO:** Imposta anche un attributo custom per CSS
            document.documentElement.classList.add('light-theme');
            document.documentElement.classList.remove('dark-theme');
            
            console.log('Tema chiaro applicato');
        }
    },

    // Alterna tra tema chiaro e scuro
    toggleTheme() {
        console.log('Toggle tema cliccato');
        
        this.states.isDark = !this.states.isDark;
        this.states.userPreference = this.states.isDark ? 'dark' : 'light';
        
        console.log('Nuovo stato:', this.states.isDark ? 'scuro' : 'chiaro');
        
        this.applyTheme();
        this.savePreference();
        this.updateUI();
    },

    // Salva le preferenze
    savePreference() {
        localStorage.setItem('infuseme-theme', this.states.userPreference);
        console.log('Preferenza salvata:', this.states.userPreference);
    },

    // Aggiorna l'interfaccia utente
    updateUI() {
        if (!this.elements.themeToggle) {
            console.warn('theme-toggle non trovato');
            return;
        }
        
        // Testi dinamici basati sullo stato corrente
        const label = this.states.isDark ? 'Attiva tema chiaro' : 'Attiva tema scuro';
        
        // Aggiorna attributi dinamicamente
        this.elements.themeToggle.setAttribute('aria-label', label);
        this.elements.themeToggle.setAttribute('title', label);
        
        // **AGGIUNTO:** Aggiorna icone se presenti
        if (this.elements.sunIcon && this.elements.moonIcon) {
            if (this.states.isDark) {
                this.elements.sunIcon.style.display = 'none';
                this.elements.moonIcon.style.display = 'block';
            } else {
                this.elements.sunIcon.style.display = 'block';
                this.elements.moonIcon.style.display = 'none';
            }
        }
        
        console.log(`UI aggiornata: ${label}`);
    },

    // Aggiungi tutti gli event listeners
    addEventListeners() {
        console.log('Aggiunta event listeners...');
        
        // **IMPORTANTE:** Aggiungi listener a TUTTI i toggle
        const allThemeToggles = document.querySelectorAll('.theme-toggle');
        
        allThemeToggles.forEach(toggle => {
            // Rimuovi listener esistenti per evitare duplicati
            const newToggle = toggle.cloneNode(true);
            toggle.parentNode.replaceChild(newToggle, toggle);
            
            newToggle.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                this.toggleTheme();
            });

            newToggle.addEventListener('keydown', (e) => {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    this.toggleTheme();
                }
            });
        });
        
        console.log(`Aggiunti ${allThemeToggles.length} toggle listeners`);
        
        // Listener per cambio preferenza sistema
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
            if (this.states.userPreference === 'system') {
                this.states.isDark = e.matches;
                this.applyTheme();
                this.updateUI();
                console.log('Tema sistema cambiato:', e.matches ? 'dark' : 'light');
            }
        });
    }
};

// **FUNZIONE DI INIZIALIZZAZIONE CORRETTA**
function initializeTheme() {
    console.log('=== INIZIALIZZAZIONE TEMA ===');
    
    // 1. APPLICA SUBITO senza aspettare DOMContentLoaded
    const savedTheme = localStorage.getItem('infuseme-theme');
    const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
    const isDark = savedTheme ? savedTheme === 'dark' : systemPrefersDark;
    
    console.log('Stato iniziale - salvato:', savedTheme, 'sistema:', systemPrefersDark);
    
    // Applica immediatamente al body
    if (isDark) {
        document.body.classList.add('dark-theme');
        document.body.classList.remove('light-theme');
        document.documentElement.setAttribute('data-theme', 'dark');
    } else {
        document.body.classList.add('light-theme');
        document.body.classList.remove('dark-theme');
        document.documentElement.setAttribute('data-theme', 'light');
    }
    
    // 2. Inizializza il manager quando il DOM è pronto
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => {
            console.log('DOMContentLoaded - inizializzo ThemeManager');
            ThemeManager.init();
        });
    } else {
        console.log('DOM già pronto - inizializzo ThemeManager immediatamente');
        ThemeManager.init();
    }
}

// **ESECUZIONE IMMEDIATA**
// Questo viene eseguito non appena il file JS viene caricato
console.log('tema.js caricato - avvio inizializzazione...');
initializeTheme();

// **AGGIUNTO:** Funzione per forzare il tema su tutte le pagine
function forceThemeRefresh() {
    console.log('Forzando refresh tema...');
    ThemeManager.loadAndApplyTheme();
}

// **AGGIUNTO:** Salvataggio aggiuntivo quando l'utente lascia la pagina
window.addEventListener('beforeunload', () => {
    if (ThemeManager.states.userPreference) {
        localStorage.setItem('infuseme-theme', ThemeManager.states.userPreference);
        console.log('Tema salvato prima di lasciare la pagina');
    }
});

// **AGGIUNTO:** Controllo se il tema è stato applicato
window.addEventListener('load', () => {
    const currentTheme = localStorage.getItem('infuseme-theme');
    const bodyTheme = document.body.classList.contains('dark-theme') ? 'dark' : 'light';
    
    console.log('=== VERIFICA TEMA ===');
    console.log('localStorage:', currentTheme);
    console.log('Body class:', bodyTheme);
    console.log('data-theme:', document.documentElement.getAttribute('data-theme'));
    
    // Se c'è incongruenza, forza il refresh
    if ((currentTheme === 'dark' && bodyTheme !== 'dark') || 
        (currentTheme === 'light' && bodyTheme !== 'light')) {
        console.log('Incosistenza rilevata, forzo correzione...');
        forceThemeRefresh();
    }
});

// Esporta per debug (solo in sviluppo)
if (typeof window !== 'undefined') {
    window.ThemeManager = ThemeManager;
    window.forceThemeRefresh = forceThemeRefresh;
}

// Fallback per browser molto vecchi
if (!window.localStorage) {
    console.warn('localStorage non supportato - le preferenze tema non verranno salvate');
}

console.log('tema.js completamente caricato e inizializzato');


/* ==========================================================================
   SEZIONE 2: MENU HAMBURGER
   ========================================================================== */
document.addEventListener('DOMContentLoaded', function() {
    
    const hamburger = document.getElementById('hamburger');
    const nav = document.querySelector('nav');
    const headerUtilities = document.querySelector('.header-utilities');
    const body = document.body;
    
    // Debug per vedere se trova gli elementi
    console.log('Hamburger trovato:', hamburger);
    console.log('Nav trovata:', nav);
    console.log('Utilities trovate:', headerUtilities);

    // Se non siamo in una pagina con header (es. login standalone), esci
    if (!hamburger || !nav || !headerUtilities) {
        console.warn('Elementi header mancanti - menu hamburger non inizializzato');
        return;
    }

    // Funzione per configurare il toggle tema nel menu mobile
    function setupMobileThemeToggle() {
        const mobileThemeToggle = document.querySelector('.mobile-menu-wrapper .theme-toggle');
        if (!mobileThemeToggle) return;
        
        // Clona per rimuovere vecchi listener ed evitare duplicati
        const newToggle = mobileThemeToggle.cloneNode(true);
        mobileThemeToggle.replaceWith(newToggle);
        
        newToggle.addEventListener('click', function(e) {
            e.stopPropagation();
            // Usa il ThemeManager se disponibile, altrimenti fallback
            if (typeof ThemeManager !== 'undefined') {
                ThemeManager.toggleTheme();
            } else {
                // Fallback se ThemeManager non è caricato
                body.classList.toggle('dark-theme');
                const isDark = body.classList.contains('dark-theme');
                localStorage.setItem('theme', isDark ? 'dark' : 'light');
                updateThemeIcons();
            }
        });
    }

    // Funzione per aggiornare le icone del tema (fallback)
    function updateThemeIcons() {
        const isDark = body.classList.contains('dark-theme');
        document.querySelectorAll('.theme-toggle').forEach(toggle => {
            const sunIcon = toggle.querySelector('.sun-icon');
            const moonIcon = toggle.querySelector('.moon-icon');
            if (sunIcon && moonIcon) {
                if (isDark) {
                    sunIcon.style.display = 'block';
                    moonIcon.style.display = 'none';
                } else {
                    sunIcon.style.display = 'none';
                    moonIcon.style.display = 'block';
                }
            }
        });
    }

    // Crea wrapper per menu mobile
    let mobileMenuWrapper = document.querySelector('.mobile-menu-wrapper');
    if (!mobileMenuWrapper) {
        mobileMenuWrapper = document.createElement('div');
        mobileMenuWrapper.className = 'mobile-menu-wrapper';
        mobileMenuWrapper.style.display = 'none';
        mobileMenuWrapper.setAttribute('role', 'dialog');
        mobileMenuWrapper.setAttribute('aria-modal', 'true');
        mobileMenuWrapper.setAttribute('aria-label', 'Menu di navigazione mobile');
        
        // Crea il pulsante di chiusura (X)
        const closeButton = document.createElement('button');
        closeButton.className = 'mobile-close-btn';
        closeButton.setAttribute('aria-label', 'Chiudi menu');
        closeButton.innerHTML = `
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true">
                <path d="M18.707 6.707l-1.414-1.414L12 10.586 6.707 5.293 5.293 6.707 10.586 12l-5.293 5.293 1.414 1.414L12 13.414l5.293 5.293 1.414-1.414L13.414 12l5.293-5.293z"/>
            </svg>
        `;
        
        // Clona nav e utilities nel wrapper
        const navClone = nav.cloneNode(true);
        const utilitiesClone = headerUtilities.cloneNode(true);
        
        mobileMenuWrapper.appendChild(closeButton);
        mobileMenuWrapper.appendChild(navClone);
        mobileMenuWrapper.appendChild(utilitiesClone);
        
        body.appendChild(mobileMenuWrapper);
        
        // Event listener per il pulsante di chiusura
        closeButton.addEventListener('click', function(e) {
            e.stopPropagation();
            closeMenu();
        });
        
        // Setup iniziale del tema mobile
        setupMobileThemeToggle();
    }

    // Toggle menu
    hamburger.addEventListener('click', function(e) {
        e.stopPropagation();
        const isOpen = hamburger.classList.contains('active');
        if (isOpen) {
            closeMenu();
        } else {
            openMenu();
        }
    });
    
    function openMenu() {
        console.log('Apertura menu...');
        hamburger.classList.add('active');
        hamburger.setAttribute('aria-expanded', 'true');
        mobileMenuWrapper.style.display = 'flex';
        
        // Previeni lo scroll del body
        body.style.overflow = 'hidden';
        
        // Riapplica il setup del tema quando il menu viene riaperto
        setTimeout(() => {
            setupMobileThemeToggle();
        }, 50);
        
        // Aggiungi listener per chiudere dopo un breve delay
        setTimeout(() => {
            document.addEventListener('click', handleClickOutside);
            mobileMenuWrapper.addEventListener('click', handleMenuClick);
        }, 100);
    }

    function closeMenu() {
        console.log('Chiusura menu...');
        hamburger.classList.remove('active');
        hamburger.setAttribute('aria-expanded', 'false');
        mobileMenuWrapper.style.display = 'none';
        
        // Ripristina lo scroll del body
        body.style.overflow = '';
        
        // Rimuovi i listener
        document.removeEventListener('click', handleClickOutside);
        mobileMenuWrapper.removeEventListener('click', handleMenuClick);
    }
    
    function handleClickOutside(e) {
        if (!mobileMenuWrapper.contains(e.target) && !hamburger.contains(e.target)) {
            closeMenu();
        }
    }
    
    function handleMenuClick(e) {
        // Chiudi se clicchi su un link o direttamente sul background
        if (e.target.tagName === 'A' || e.target === mobileMenuWrapper) {
            closeMenu();
        }
    }
    
    // Chiudi con tasto ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && hamburger.classList.contains('active')) {
            closeMenu();
        }
    });
    
    // Chiudi se si ridimensiona la finestra a desktop
    let resizeTimer;
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function() {
            console.log('Resize detected:', window.innerWidth);
            if (window.innerWidth > 900 && hamburger.classList.contains('active')) {
                closeMenu();
            }
        }, 250);
    });
});

/* ==========================================================================
   SEZIONE 3: PULSANTE TORNA SU 
   ========================================================================== */
(function() {
    'use strict';
    // Seleziona il pulsante
    const backToTopButton = document.getElementById('backToTop');
    
    // Verifica esistenza
    if (!backToTopButton) {
        console.warn('Pulsante back-to-top non trovato');
        return;
    }
    // Soglia scroll in pixel
    const SCROLL_THRESHOLD = 300;
    
    /* Mostra/nascondi pulsante in base allo scroll */
    function toggleButtonVisibility() {
        const scrollY = window.scrollY || window.pageYOffset;
        if (scrollY > SCROLL_THRESHOLD) {
            backToTopButton.classList.add('visible');
        } else {
            backToTopButton.classList.remove('visible');
        }
    }
    
    /* Scroll fluido verso l'alto */
    function scrollToTop(event) {
        event.preventDefault();
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });

        // ACCESSIBILITÀ: Focus sul skip link dopo scroll
        // (migliora navigazione tastiera) - opzionale
        setTimeout(function() {
            const skipLink = document.querySelector('.skip-link');
            if (skipLink) {
                skipLink.focus();
            }
        }, 500);
    }
    
    // Event Listeners
    window.addEventListener('scroll', toggleButtonVisibility, { passive: true });
    backToTopButton.addEventListener('click', scrollToTop);
    
    // Controllo iniziale al caricamento
    toggleButtonVisibility();

})();


/* ==========================================================================
   SEZIONE 4: VALIDAZIONE FORM 
   ========================================================================== */
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

function caricamentoForm() {
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

// Avvio validazione form
window.addEventListener("load", caricamentoForm);



/* ==========================================================================
   SEZIONE 5: CARRELLO CATALOGO 
   ========================================================================== */
document.addEventListener('DOMContentLoaded', function() {
    const cartButtons = document.querySelectorAll('.aggiungi-carrello');
    // Se non ci sono bottoni carrello, non fare nulla ma aggiorna counter se esiste
    updateCartCounter();

    if (cartButtons.length > 0) {
        cartButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                
                const productId = this.getAttribute('data-id');
                const productName = this.getAttribute('data-nome');
                const productPrice = this.getAttribute('data-prezzo');
                const productImg = this.getAttribute('data-img');
                
                // Aggiungi prodotto al carrello (localStorage)
                addToCart(productId, productName, productPrice, productImg);
                
                // Feedback visivo
                const originalText = this.innerHTML;
                this.innerHTML = "<span style='color:white'>✓ Aggiunto!</span>";
                this.classList.add('aggiunto');
                
                // Ripristina dopo 2 secondi
                setTimeout(() => {
                    this.innerHTML = originalText;
                    this.classList.remove('aggiunto');
                }, 2000);
                
                // Aggiorna contatore carrello
                updateCartCounter();
            });
        });
    }

    // Funzione per aggiungere al carrello
    function addToCart(id, name, price, img) {
        let cart = JSON.parse(localStorage.getItem('carrello')) || [];

        //controlla se il prodotto è già nel carrello
        const existingItem = cart.find(item => item.id === id);
        
        if (existingItem) {
            existingItem.quantity += 1;
        } else {
            cart.push({
                id: id,
                name: name,
                price: parseFloat(price),
                img: img,
                quantity: 1
            });
        }
        localStorage.setItem('carrello', JSON.stringify(cart));
        console.log('Carrello aggiornato:', cart);
    }
    
    // Funzione per aggiornare contatore carrello
    function updateCartCounter() {
        const cart = JSON.parse(localStorage.getItem('carrello')) || [];
        const totalItems = cart.reduce((total, item) => total + item.quantity, 0);
        
        // Se hai un contatore nel carrello nell'header, aggiornalo
        const cartCounter = document.querySelector('.cart-counter');
        if (cartCounter) {
            cartCounter.textContent = totalItems;
            cartCounter.style.display = totalItems > 0 ? 'inline' : 'none';
        }
    }
});

/* ==========================================================================
   SEZIONE 6.1: CONTROLLO QUANTITÀ PRODOTTO
   ========================================================================== */
document.addEventListener('DOMContentLoaded', function() {
    // Funzione per il controllo quantità
    function initQuantityControls() {
        const minusBtn = document.querySelector('.quantity-btn.minus');
        const plusBtn = document.querySelector('.quantity-btn.plus');
        const input = document.querySelector('#quantita');
        
        if (minusBtn && plusBtn && input) {
            minusBtn.addEventListener('click', () => {
                let value = parseInt(input.value);
                if (value > parseInt(input.min)) {
                    input.value = value - 1;
                    toggleButtonsState(input, minusBtn, plusBtn);
                }
            });
            
            plusBtn.addEventListener('click', () => {
                let value = parseInt(input.value);
                if (value < parseInt(input.max)) {
                    input.value = value + 1;
                    toggleButtonsState(input, minusBtn, plusBtn);
                }
            });
            
            // Disabilita pulsanti all'inizio se necessario
            toggleButtonsState(input, minusBtn, plusBtn);
        }
    }

    function toggleButtonsState(input, minusBtn, plusBtn) {
        minusBtn.disabled = parseInt(input.value) <= parseInt(input.min);
        plusBtn.disabled = parseInt(input.value) >= parseInt(input.max);
    }

    // Inizializza i controlli quantità quando il DOM è caricato
    initQuantityControls();
});

// Gestione Aggiunta al Carrello con quantità
document.addEventListener('DOMContentLoaded', function() {
    const aggiungiCarrelloBtn = document.getElementById('aggiungiCarrello');
    
    if (aggiungiCarrelloBtn) {
        aggiungiCarrelloBtn.addEventListener('click', function() {
            const idProdotto = this.getAttribute('data-id');
            const nome = this.getAttribute('data-nome');
            const prezzo = parseFloat(this.getAttribute('data-prezzo'));
            const img = this.getAttribute('data-img');
            const disponibilita = parseInt(this.getAttribute('data-disponibilita'));
            const quantitaInput = document.getElementById('quantita');
            const quantita = quantitaInput ? parseInt(quantitaInput.value) : 1;
            
            // Verifica disponibilità
            if (quantita > disponibilita) {
                alert('Quantità non disponibile! Disponibilità attuale: ' + disponibilita);
                return;
            }
            
            // Aggiungi al carrello
            addToCartWithQuantity(idProdotto, nome, prezzo, img, quantita);
            
            // Feedback visivo
            const originalText = this.innerHTML;
            this.innerHTML = "<span style='color:white'>✓ Aggiunto!</span>";
            this.classList.add('aggiunto');
            
            // Ripristina dopo 2 secondi
            setTimeout(() => {
                this.innerHTML = originalText;
                this.classList.remove('aggiunto');
            }, 2000);
            
            // Aggiorna contatore carrello
            updateCartCounter();
        });
    }
});

// Funzione per aggiungere al carrello con quantità
function addToCartWithQuantity(id, name, price, img, quantity) {
    let cart = JSON.parse(localStorage.getItem('carrello')) || [];

    // Controlla se il prodotto è già nel carrello
    const existingItem = cart.find(item => item.id === id);
    
    if (existingItem) {
        existingItem.quantity += quantity;
    } else {
        cart.push({
            id: id,
            name: name,
            price: parseFloat(price),
            img: img,
            quantity: quantity
        });
    }
    localStorage.setItem('carrello', JSON.stringify(cart));
    console.log('Carrello aggiornato:', cart);
}

// Funzione per aggiornare contatore carrello
function updateCartCounter() {
    const cart = JSON.parse(localStorage.getItem('carrello')) || [];
    const totalItems = cart.reduce((total, item) => total + item.quantity, 0);
    
    // Se hai un contatore nel carrello nell'header, aggiornalo
    const cartCounter = document.querySelector('.cart-counter');
    if (cartCounter) {
        cartCounter.textContent = totalItems;
        cartCounter.style.display = totalItems > 0 ? 'inline' : 'none';
    }
}


/* ==========================================================================
   SEZIONE 7: CREA BLEND 
   ========================================================================== */
document.addEventListener('DOMContentLoaded', function() {
    // Stato della selezione
    const statoBlend = {
        base: null,
        ingredienti: [],
        maxIngredienti: 2, // Default
        prezzoBase: 2.50,
        prezzoIngrediente: 1.00
    };
    
    // Elementi DOM
    const contatoreBase = document.getElementById('contatore-base');
    const contatoreIngredienti = document.getElementById('contatore-ingredienti');
    const baseSelezionataDiv = document.getElementById('base-selezionata');
    const ingredientiSelezionatiDiv = document.getElementById('ingredienti-selezionati');
    const importoPrezzo = document.getElementById('importo-prezzo');
    const btnConferma = document.getElementById('btn-conferma');
    const btnReset = document.getElementById('btn-reset');
    const radio2 = document.getElementById('radio-2');
    const radio3 = document.getElementById('radio-3');
    
    // Form nascosto
    const formBlend = document.getElementById('form-blend');
    const inputIdBase = document.getElementById('input-id-base');
    const inputIngredienti = document.getElementById('input-ingredienti');
    const inputNomeBlend = document.getElementById('input-nome-blend');
    const inputPrezzo = document.getElementById('input-prezzo');
    
    function aggiornaMaxIngredienti() {
        const selectedRadio = document.querySelector('input[name="numIngredienti"]:checked');
        if (selectedRadio) {
            statoBlend.maxIngredienti = parseInt(selectedRadio.value);
            aggiornaContatori();
            aggiornaStatoBottone();
            aggiornaCardIngredienti();
        }
    }
    
    // Aggiorna i contatori nel riepilogo
    function aggiornaContatori() {
        // Base
        const baseSelezionata = statoBlend.base ? 1 : 0;
        contatoreBase.textContent = `${baseSelezionata}/1`;
        contatoreBase.className = baseSelezionata === 1 ? 'contatore completo' : 'contatore';
        
        // Ingredienti
        const numIngredienti = statoBlend.ingredienti.length;
        contatoreIngredienti.textContent = `${numIngredienti}/${statoBlend.maxIngredienti}`;
        contatoreIngredienti.className = numIngredienti >= statoBlend.maxIngredienti ? 
            'contatore completo' : 'contatore';
        
        // Aggiorna prezzo
        aggiornaPrezzo();
    }
    
    // Aggiorna il prezzo totale
    function aggiornaPrezzo() {
        let prezzoTotale = 0;
        
        if (statoBlend.base) {
            prezzoTotale += statoBlend.prezzoBase;
        }
        
        prezzoTotale += statoBlend.ingredienti.length * statoBlend.prezzoIngrediente;
        
        // Sconto se ha 3 ingredienti
        if (statoBlend.ingredienti.length === 3) {
            prezzoTotale -= 0.50; // Sconto di 0.50€
        }
        
        importoPrezzo.textContent = prezzoTotale.toFixed(2);
    }
    
    function aggiornaRiepilogo() {
        if (statoBlend.base) {
            baseSelezionataDiv.innerHTML = `
                <h4>Base:</h4>
                <div class="elemento-selezionato">
                    <strong>${statoBlend.base.nome}</strong>
                    <span class="prezzo-elemento">€${statoBlend.prezzoBase.toFixed(2)}</span>
                </div>
            `;
        } else {
            baseSelezionataDiv.innerHTML = `
                <h4>Base:</h4>
                <p class="nessuna-selezione">Nessuna base selezionata</p>
            `;
        }
        
        if (statoBlend.ingredienti.length > 0) {
            let html = '<h4>Ingredienti:</h4>';
            statoBlend.ingredienti.forEach(ing => {
                html += `
                <div class="elemento-selezionato">
                    <strong>${ing.nome}</strong>
                    <button class="btn-rimuovi" data-id="${ing.id}">✕</button>
                </div>
                `;
            });
            ingredientiSelezionatiDiv.innerHTML = html;
            
            document.querySelectorAll('.btn-rimuovi').forEach(btn => {
                btn.addEventListener('click', function() {
                    const id = parseInt(this.getAttribute('data-id'));
                    rimuoviIngrediente(id);
                });
            });
        } else {
            ingredientiSelezionatiDiv.innerHTML = `
                <h4>Ingredienti:</h4>
                <p class="nessuna-selezione">Nessun ingrediente selezionato</p>
            `;
        }
    }
    
    //Aggiorna lo stato dlle card ingredienti
    function aggiornaCardIngredienti() {
        const cardIngredienti = document.querySelectorAll('.ingrediente-card');
        const numSelezionati = statoBlend.ingredienti.length;
        
        cardIngredienti.forEach(card => {
            const id = parseInt(card.getAttribute('data-id'));
            const isSelezionato = statoBlend.ingredienti.some(ing => ing.id === id);
            const btn = card.querySelector('.btn-aggiungi-ingrediente');
            
            if (isSelezionato) {
                card.classList.add('selezionato');
                btn.textContent = 'Rimuovi';
                btn.classList.add('selezionato');
            } else {
                card.classList.remove('selezionato');
                btn.textContent = 'Aggiungi Ingrediente';
                btn.classList.remove('selezionato');
                
                // Disabilita se abbiamo raggiunto il massimo
                if (numSelezionati >= statoBlend.maxIngredienti) {
                    card.classList.add('disabilitato');
                    btn.disabled = true;
                } else {
                    card.classList.remove('disabilitato');
                    btn.disabled = false;
                }
            }
        });
    }
    
    // Aggiorna lo stato del bottone conferma
    function aggiornaStatoBottone() {
        const baseOk = statoBlend.base !== null;
        const ingredientiOk = statoBlend.ingredienti.length >= 2 && 
                             statoBlend.ingredienti.length <= statoBlend.maxIngredienti;
        
        if (baseOk && ingredientiOk) {
            btnConferma.disabled = false;
            btnConferma.classList.remove('disabilitato');
        } else {
            btnConferma.disabled = true;
            btnConferma.classList.add('disabilitato');
        }
    }
    
    // SELEZIONE BASE
    function inizializzaSelezioneBase() {
        document.querySelectorAll('.btn-seleziona-base').forEach(btn => {
            btn.addEventListener('click', function() {
                const card = this.closest('.base-card');
                const id = parseInt(card.getAttribute('data-id'));
                const nome = card.getAttribute('data-nome');
                
                // Deseleziona tutte le altre basi
                document.querySelectorAll('.base-card').forEach(c => {
                    c.classList.remove('selezionato');
                    const otherBtn = c.querySelector('.btn-seleziona-base');
                    if (otherBtn) otherBtn.textContent = 'Seleziona Base';
                });
                
                // Seleziona questa base
                card.classList.add('selezionato');
                this.textContent = 'Selezionata ✓';
                
                // Aggiorna stato
                statoBlend.base = { id, nome };
                
                // Aggiorna UI
                aggiornaContatori();
                aggiornaRiepilogo();
                aggiornaStatoBottone();
            });
        });
    }
    
    // SELEZIONE INGREDIENTI
    function inizializzaSelezioneIngredienti() {
        document.querySelectorAll('.btn-aggiungi-ingrediente').forEach(btn => {
            btn.addEventListener('click', function() {
                const card = this.closest('.ingrediente-card');
                if (!card || card.classList.contains('disabilitato')) return;
                
                const id = parseInt(card.getAttribute('data-id'));
                const nome = card.getAttribute('data-nome');
                const tipo = card.getAttribute('data-tipo');
                
                // Controlla se è già selezionato
                const index = statoBlend.ingredienti.findIndex(ing => ing.id === id);
                
                if (index > -1) {
                    // Rimuovi
                    statoBlend.ingredienti.splice(index, 1);
                    card.classList.remove('selezionato');
                    this.textContent = 'Aggiungi Ingrediente';
                    this.classList.remove('selezionato');
                } else {
                    // Aggiungi se non abbiamo raggiunto il massimo
                    if (statoBlend.ingredienti.length < statoBlend.maxIngredienti) {
                        statoBlend.ingredienti.push({ id, nome, tipo });
                        card.classList.add('selezionato');
                        this.textContent = 'Rimuovi';
                        this.classList.add('selezionato');
                    }
                }
                
                // Aggiorna UI
                aggiornaContatori();
                aggiornaRiepilogo();
                aggiornaCardIngredienti();
                aggiornaStatoBottone();
            });
        });
    }
    
    // Funzione per rimuovere ingrediente dal riepilogo
    function rimuoviIngrediente(id) {
        const index = statoBlend.ingredienti.findIndex(ing => ing.id === id);
        if (index > -1) {
            statoBlend.ingredienti.splice(index, 1);
            
            // Aggiorna anche la card corrispondente
            const card = document.querySelector(`.ingrediente-card[data-id="${id}"]`);
            if (card) {
                card.classList.remove('selezionato');
                const btn = card.querySelector('.btn-aggiungi-ingrediente');
                if (btn) {
                    btn.textContent = 'Aggiungi Ingrediente';
                    btn.classList.remove('selezionato');
                }
            }
            
            // Aggiorna UI
            aggiornaContatori();
            aggiornaRiepilogo();
            aggiornaCardIngredienti();
            aggiornaStatoBottone();
        }
    }
    
    function inizializzaReset() {
        if (!btnReset) return;
        
        btnReset.addEventListener('click', function() {
            statoBlend.base = null;
            statoBlend.ingredienti = [];
            
            document.querySelectorAll('.base-card').forEach(card => {
                card.classList.remove('selezionato');
                const btn = card.querySelector('.btn-seleziona-base');
                if (btn) btn.textContent = 'Seleziona Base';
            });
            
            document.querySelectorAll('.ingrediente-card').forEach(card => {
                card.classList.remove('selezionato', 'disabilitato');
                const btn = card.querySelector('.btn-aggiungi-ingrediente');
                if (btn) {
                    btn.textContent = 'Aggiungi Ingrediente';
                    btn.classList.remove('selezionato');
                    btn.disabled = false;
                }
            });
            
            if (radio2) radio2.checked = true;
            statoBlend.maxIngredienti = 2;
            
            aggiornaContatori();
            aggiornaRiepilogo();
            aggiornaCardIngredienti();
            aggiornaStatoBottone();
        });
    }
    
    // CONFERMA BLEND
    function inizializzaConferma() {
        if (!btnConferma) return;
        
        btnConferma.addEventListener('click', function() {
            if (btnConferma.disabled) return;
            
            if (!statoBlend.base || statoBlend.ingredienti.length < 2) {
                alert('Seleziona una base e almeno 2 ingredienti!');
                return;
            }
            
            const nomeBlend = statoBlend.base.nome + " con " + 
                             statoBlend.ingredienti.map(i => i.nome).join(", ");
            
            if (inputIdBase) inputIdBase.value = statoBlend.base.id;
            if (inputIngredienti) inputIngredienti.value = JSON.stringify(statoBlend.ingredienti.map(i => i.id));
            if (inputNomeBlend) inputNomeBlend.value = nomeBlend;
            if (inputPrezzo) inputPrezzo.value = parseFloat(importoPrezzo.textContent);
            
            if (formBlend) {
                formBlend.submit();
            } else {
                console.log('Dati del blend:', {
                    base: statoBlend.base,
                    ingredienti: statoBlend.ingredienti,
                    prezzo: parseFloat(importoPrezzo.textContent)
                });
                alert('Blend pronto! Dati: ' + nomeBlend);
            }
        });
    }
    
    function inizializza() {
        if (radio2) radio2.addEventListener('change', aggiornaMaxIngredienti);
        if (radio3) radio3.addEventListener('change', aggiornaMaxIngredienti);
        
        inizializzaSelezioneBase();
        inizializzaSelezioneIngredienti();
        inizializzaReset();
        inizializzaConferma();
        
        aggiornaContatori();
        aggiornaRiepilogo();
        aggiornaCardIngredienti();
        aggiornaStatoBottone();
    }
    
    inizializza();
    window.statoBlend = statoBlend;
});

// ===================================
// GESTIONE FILTRI E ORDINAMENTO CATALOGO
// ===================================

document.addEventListener('DOMContentLoaded', function() {
    
    // TOGGLE FILTRI MOBILE
    const toggleFiltersBtn = document.getElementById('toggleFilters');
    const filterPanel = document.getElementById('filterPanel');
    
    if (toggleFiltersBtn && filterPanel) {
        toggleFiltersBtn.addEventListener('click', function() {
            const isOpen = filterPanel.classList.contains('open');
            
            if (isOpen) {
                filterPanel.classList.remove('open');
                toggleFiltersBtn.setAttribute('aria-expanded', 'false');
            } else {
                filterPanel.classList.add('open');
                toggleFiltersBtn.setAttribute('aria-expanded', 'true');
            }
        });
    }
    
    // ELEMENTI DOM
    const productContainer = document.getElementById('productContainer');
    const noResultsMsg = document.getElementById('noResults');
    const allProducts = document.querySelectorAll('.product-card');
    
    // Radio buttons categorie
    const categoryRadios = document.querySelectorAll('input[name="category"]');
    
    // Checkbox ingredienti
    const ingredientCheckboxes = document.querySelectorAll('.ing-filter');
    
    // Radio buttons prezzo
    const priceRadios = document.querySelectorAll('input[name="priceRange"]');
    
    // Radio buttons base
    const baseRadios = document.querySelectorAll('input[name="baseFilter"]');
    
    // Radio buttons ordinamento
    const sortRadios = document.querySelectorAll('input[name="sortOrder"]');
    
    // FUNZIONE PRINCIPALE FILTRAGGIO
    function filterProducts() {
        // Ottieni filtri selezionati
        const selectedCategory = document.querySelector('input[name="category"]:checked')?.value || 'all';
        const selectedPriceRange = document.querySelector('input[name="priceRange"]:checked')?.value || 'all';
        const selectedBase = document.querySelector('input[name="baseFilter"]:checked')?.value || 'all';
        
        // Ottieni ingredienti selezionati
        const selectedIngredients = Array.from(ingredientCheckboxes)
            .filter(cb => cb.checked)
            .map(cb => cb.value.toLowerCase());
        
        let visibleCount = 0;
        
        // Filtra ogni prodotto
        allProducts.forEach(product => {
            let shouldShow = true;
            
            // FILTRO CATEGORIA
            if (selectedCategory !== 'all') {
                const productCategory = product.dataset.category?.toLowerCase() || '';
                if (productCategory !== selectedCategory) {
                    shouldShow = false;
                }
            }
            
            // FILTRO PREZZO
            if (selectedPriceRange !== 'all') {
                const price = parseFloat(product.dataset.price) || 0;
                
                if (selectedPriceRange === 'low' && price > 5) {
                    shouldShow = false;
                } else if (selectedPriceRange === 'medium' && (price <= 5 || price > 10)) {
                    shouldShow = false;
                } else if (selectedPriceRange === 'high' && price <= 10) {
                    shouldShow = false;
                }
            }
            
            // FILTRO BASE
            if (selectedBase !== 'all') {
                const productBase = product.dataset.base?.toLowerCase() || '';
                if (!productBase.includes(selectedBase)) {
                    shouldShow = false;
                }
            }
            
            // FILTRO INGREDIENTI
            if (selectedIngredients.length > 0) {
                const productIngredients = product.dataset.ingredients?.toLowerCase() || '';
                
                // Il prodotto deve contenere TUTTI gli ingredienti selezionati
                const hasAllIngredients = selectedIngredients.every(ing => 
                    productIngredients.includes(ing)
                );
                
                if (!hasAllIngredients) {
                    shouldShow = false;
                }
            }
            
            // Mostra/nascondi prodotto
            if (shouldShow) {
                product.style.display = '';
                visibleCount++;
            } else {
                product.style.display = 'none';
            }
        });
        
        // Mostra messaggio "nessun risultato"
        if (visibleCount === 0) {
            noResultsMsg.style.display = 'block';
        } else {
            noResultsMsg.style.display = 'none';
        }
        
        // Dopo il filtraggio, applica l'ordinamento
        const selectedSort = document.querySelector('input[name="sortOrder"]:checked')?.value || 'default';
        sortProducts(selectedSort);
    }
    
    // FUNZIONE ORDINAMENTO
    function sortProducts(sortType) {
        // Converti NodeList in Array
        const productsArray = Array.from(allProducts);
        
        // Filtra solo i prodotti visibili
        const visibleProducts = productsArray.filter(p => p.style.display !== 'none');
        
        // Ordina in base al tipo
        visibleProducts.sort((a, b) => {
            switch(sortType) {
                case 'priceAsc':
                    return parseFloat(a.dataset.price) - parseFloat(b.dataset.price);
                
                case 'priceDesc':
                    return parseFloat(b.dataset.price) - parseFloat(a.dataset.price);
                
                case 'nameAsc':
                    return a.dataset.name.localeCompare(b.dataset.name, 'it');
                
                case 'nameDesc':
                    return b.dataset.name.localeCompare(a.dataset.name, 'it');
                
                case 'default':
                default:
                    // Ordine originale (non fare nulla)
                    return 0;
            }
        });
        
        // Riordina nel DOM solo i prodotti visibili
        visibleProducts.forEach(product => {
            productContainer.appendChild(product);
        });
    }
    
    // EVENT LISTENERS
    // Filtro Categoria
    categoryRadios.forEach(radio => {
        radio.addEventListener('change', filterProducts);
    });
    
    // Filtro Ingredienti
    ingredientCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', filterProducts);
    });
    
    // Filtro Prezzo
    priceRadios.forEach(radio => {
        radio.addEventListener('change', filterProducts);
    });
    
    // Filtro Base
    baseRadios.forEach(radio => {
        radio.addEventListener('change', filterProducts);
    });
    
    // Ordinamento
    sortRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            sortProducts(this.value);
        });
    });
    
    // RESET FILTRI (opzionale)
    // Puoi aggiungere un pulsante "Reset Filtri" nell'HTML
    const resetBtn = document.getElementById('resetFilters');
    if (resetBtn) {
        resetBtn.addEventListener('click', function() {
            // Reset categoria
            document.querySelector('input[name="category"][value="all"]').checked = true;
            
            // Reset prezzo
            document.querySelector('input[name="priceRange"][value="all"]').checked = true;
            
            // Reset base
            document.querySelector('input[name="baseFilter"][value="all"]').checked = true;
            
            // Reset ingredienti
            ingredientCheckboxes.forEach(cb => cb.checked = false);
            
            // Reset ordinamento
            document.querySelector('input[name="sortOrder"][value="default"]').checked = true;
            
            // Riapplica filtri (mostra tutto)
            filterProducts();
        });
    }
    
    // INIZIALIZZAZIONE
    // Applica filtri all'avvio (per sicurezza)
    filterProducts();
    
    console.log('Sistema filtri e ordinamento catalogo caricato');
});

// FUNZIONI HELPER AGGIUNTIVE

// Conta prodotti visibili
function countVisibleProducts() {
    const visible = document.querySelectorAll('.product-card:not([style*="display: none"])');
    return visible.length;
}

// Debug: mostra stato filtri corrente
function debugFilters() {
    console.log('=== STATO FILTRI ===');
    console.log('Categoria:', document.querySelector('input[name="category"]:checked')?.value);
    console.log('Prezzo:', document.querySelector('input[name="priceRange"]:checked')?.value);
    console.log('Base:', document.querySelector('input[name="baseFilter"]:checked')?.value);
    
    const selectedIngs = Array.from(document.querySelectorAll('.ing-filter:checked'))
        .map(cb => cb.value);
    console.log('Ingredienti:', selectedIngs);
    
    console.log('Ordinamento:', document.querySelector('input[name="sortOrder"]:checked')?.value);
    console.log('Prodotti visibili:', countVisibleProducts());
    console.log('===================');
}


