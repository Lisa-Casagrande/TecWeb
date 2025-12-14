document.addEventListener('DOMContentLoaded', function() {
    // Stato della selezione
    const statoBlend = {
        base: null,
        ingredienti: [],
        maxIngredienti: 2, // Default
        prezzoBase: 3.50,
        prezzoIngrediente: 1.50
    };
    
    // Elementi DOM
    const riepilogoFisso = document.getElementById('riepilogoFisso');
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
    
    // Aggiorna il contatore massimo di ingredienti
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
    
    // Aggiorna il riepilogo visivo
    function aggiornaRiepilogo() {
        // Base selezionata
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
        
        // Ingredienti selezionati
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
            
            // Aggiungi event listener ai pulsanti rimuovi
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
    
    // Aggiorna lo stato delle card ingredienti
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
    
    // RESET SELEZIONE
    function inizializzaReset() {
        if (!btnReset) return;
        
        btnReset.addEventListener('click', function() {
            // Reset stato
            statoBlend.base = null;
            statoBlend.ingredienti = [];
            
            // Reset UI basi
            document.querySelectorAll('.base-card').forEach(card => {
                card.classList.remove('selezionato');
                const btn = card.querySelector('.btn-seleziona-base');
                if (btn) btn.textContent = 'Seleziona Base';
            });
            
            // Reset UI ingredienti
            document.querySelectorAll('.ingrediente-card').forEach(card => {
                card.classList.remove('selezionato', 'disabilitato');
                const btn = card.querySelector('.btn-aggiungi-ingrediente');
                if (btn) {
                    btn.textContent = 'Aggiungi Ingrediente';
                    btn.classList.remove('selezionato');
                    btn.disabled = false;
                }
            });
            
            // Ripristina radio 2 ingredienti
            if (radio2) radio2.checked = true;
            statoBlend.maxIngredienti = 2;
            
            // Aggiorna tutto
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
            
            // Verifica che abbiamo tutti i dati necessari
            if (!statoBlend.base || statoBlend.ingredienti.length < 2) {
                alert('Seleziona una base e almeno 2 ingredienti!');
                return;
            }
            
            // Prepara dati per il form
            const nomeBlend = statoBlend.base.nome + " con " + 
                             statoBlend.ingredienti.map(i => i.nome).join(", ");
            
            if (inputIdBase) inputIdBase.value = statoBlend.base.id;
            if (inputIngredienti) inputIngredienti.value = JSON.stringify(statoBlend.ingredienti.map(i => i.id));
            if (inputNomeBlend) inputNomeBlend.value = nomeBlend;
            if (inputPrezzo) inputPrezzo.value = parseFloat(importoPrezzo.textContent);
            
            // Invia il form se esiste
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
    
    // Inizializza event listeners
    function inizializza() {
        // Radio buttons per numero ingredienti
        if (radio2) {
            radio2.addEventListener('change', aggiornaMaxIngredienti);
        }
        if (radio3) {
            radio3.addEventListener('change', aggiornaMaxIngredienti);
        }
        
        // Inizializza le funzionalità
        inizializzaSelezioneBase();
        inizializzaSelezioneIngredienti();
        inizializzaReset();
        inizializzaConferma();
        
        // Aggiorna UI iniziale
        aggiornaContatori();
        aggiornaRiepilogo();
        aggiornaCardIngredienti();
        aggiornaStatoBottone();
    }
    
    // Avvia tutto
    inizializza();
    
    // Espone lo stato per debug (opzionale)
    window.statoBlend = statoBlend;
});