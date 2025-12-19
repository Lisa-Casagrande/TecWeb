/**
 * tema.js - Gestione tema chiaro/scuro per InfuseMe
 * Implementazione accessibile e user-friendly
 * VERSIONE CORRETTA - Persistenza tema fixata
 */

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
        this.elements.sunIcon = document.querySelector('.sun-icon');
        this.elements.moonIcon = document.querySelector('.moon-icon');

        // APPLICA IL TEMA SUBITO all'avvio
        this.loadAndApplyTheme();

        // Aggiungi event listeners
        this.addEventListeners();

        // Aggiorna stato iniziale
        this.updateUI();

        // Setup per menu mobile (se presente)
        this.setupMobileThemeToggle();
    },

    // Carica e applica il tema dalle preferenze - MODIFICATA
    loadAndApplyTheme() {
        // 1. Controlla preferenza utente salvata
        const savedTheme = localStorage.getItem('infuseme-theme');
        
        // 2. Controlla preferenza sistema
        const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        
        // 3. Logica di priorità
        if (savedTheme) {
            this.states.userPreference = savedTheme;
            this.states.isDark = savedTheme === 'dark';
        } else {
            this.states.userPreference = 'system';
            this.states.isDark = systemPrefersDark;
        }

        // 4. APPLICA IMMEDIATAMENTE il tema al DOM
        this.applyTheme();

        console.log(`Tema caricato: ${this.states.userPreference} (dark: ${this.states.isDark})`);
    },

    // Alterna tra tema chiaro e scuro
    toggleTheme() {
        // Inverte lo stato
        this.states.isDark = !this.states.isDark;
        
        // Salva come preferenza utente esplicita
        this.states.userPreference = this.states.isDark ? 'dark' : 'light';
        
        // Applica il tema
        this.applyTheme();
        
        // Salva nelle preferenze
        this.savePreference();
        
        // Aggiorna UI
        this.updateUI();

        console.log(`Tema cambiato a: ${this.states.isDark ? 'scuro' : 'chiaro'}`);
    },

    // Applica il tema al DOM - MODIFICATA per immediatezza
    applyTheme() {
        if (this.states.isDark) {
            this.elements.body.classList.add('dark-theme');
            document.documentElement.setAttribute('data-theme', 'dark');
        } else {
            this.elements.body.classList.remove('dark-theme');
            document.documentElement.setAttribute('data-theme', 'light');
        }
    },

    // Salva le preferenze
    savePreference() {
        localStorage.setItem('infuseme-theme', this.states.userPreference);
    },

    // Aggiorna l'interfaccia utente
    updateUI() {
        if (!this.elements.themeToggle) return;

        // Testi dinamici basati sullo stato corrente
        const label = this.states.isDark ? 
            'Attiva tema chiaro' : 
            'Attiva tema scuro';
        
        const title = this.states.isDark ?
            'Passa al tema chiaro' :
            'Passa al tema scuro';

        // Aggiorna attributi dinamicamente
        this.elements.themeToggle.setAttribute('aria-label', label); //per screen reader
        this.elements.themeToggle.setAttribute('title', title);

        console.log(`UI aggiornata: ${label}`);
    },

    // Setup per tema nel menu mobile - NUOVA FUNZIONE
    setupMobileThemeToggle() {
        // Funzione per setup mobile
        const setupMobile = () => {
            const mobileThemeToggle = document.querySelector('.mobile-menu-wrapper .theme-toggle');
            if (mobileThemeToggle) {
                // Rimuovi eventuali listener duplicati
                const newToggle = mobileThemeToggle.cloneNode(true);
                mobileThemeToggle.parentNode.replaceChild(newToggle, mobileThemeToggle);
                
                newToggle.addEventListener('click', () => {
                    console.log('Toggle tema mobile cliccato');
                    this.toggleTheme();
                });
            }
        };

        // Setup immediato e anche quando il menu mobile viene aperto
        setTimeout(setupMobile, 100);
        
        // Riascolta periodicamente per menu mobile dinamico
        setInterval(setupMobile, 1000);
    },

    // Aggiungi tutti gli event listeners
    addEventListeners() {
        // Click sul toggle principale
        if (this.elements.themeToggle) {
            this.elements.themeToggle.addEventListener('click', () => {
                this.toggleTheme();
            });

            // Supporto per tastiera (Enter/Space)
            this.elements.themeToggle.addEventListener('keydown', (e) => {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    this.toggleTheme();
                }
            });
        }

        // Ascolta cambiamenti preferenza sistema (solo se utente non ha scelto)
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
            if (this.states.userPreference === 'system') {
                this.states.isDark = e.matches;
                this.applyTheme();
                this.updateUI();
                console.log('Tema sistema cambiato');
            }
        });
    },

    // Metodo per ottenere stato corrente (utile per debug)
    getStatus() {
        return {
            isDark: this.states.isDark,
            userPreference: this.states.userPreference,
            systemPrefersDark: window.matchMedia('(prefers-color-scheme: dark)').matches
        };
    }
};

// INIZIALIZZAZIONE IMMEDIATA - MODIFICATA per performance
function initializeTheme() {
    // Applica il tema il prima possibile per evitare flash
    const savedTheme = localStorage.getItem('infuseme-theme');
    const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
    const isDark = savedTheme ? savedTheme === 'dark' : systemPrefersDark;
    
    if (isDark) {
        document.body.classList.add('dark-theme');
        document.documentElement.setAttribute('data-theme', 'dark');
    } else {
        document.body.classList.remove('dark-theme');
        document.documentElement.setAttribute('data-theme', 'light');
    }

    // Poi inizializza il manager completo quando il DOM è pronto
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => ThemeManager.init());
    } else {
        ThemeManager.init();
    }
}

// Avvia l'inizializzazione
initializeTheme();

// Esporta per debug (solo in sviluppo)
if (typeof window !== 'undefined') {
    window.ThemeManager = ThemeManager;
}

// Fallback per browser molto vecchi
if (!window.localStorage) {
    console.warn('localStorage non supportato - le preferenze tema non verranno salvate');
}

console.log('tema.js caricato correttamente');
