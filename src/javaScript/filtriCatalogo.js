// javaScript/filtriCatalogo.js

document.addEventListener('DOMContentLoaded', function() {
    
    // Elementi DOM
    const toggleBtn = document.getElementById('toggleFilters');
    const filterPanel = document.getElementById('filterPanel');
    const cards = Array.from(document.querySelectorAll('.product-card'));
    const noResults = document.getElementById('noResults');
    const sortSelect = document.getElementById('sortOrder');
    
    console.log('Prodotti caricati:', cards.length);
    
    // 1. APRI/CHIUDI PANNELLO FILTRI (MOBILE)
    if (toggleBtn && filterPanel) {
        toggleBtn.addEventListener('click', () => {
            const isOpen = filterPanel.classList.toggle('open');
            toggleBtn.setAttribute('aria-expanded', isOpen);
        });
    }
    
    // 2. LOGICA FILTRAGGIO
    const categoryInputs = document.querySelectorAll('input[name="category"]');
    const ingredientInputs = document.querySelectorAll('.ing-filter');
    const priceInputs = document.querySelectorAll('input[name="priceRange"]');
    const baseInputs = document.querySelectorAll('input[name="baseFilter"]');
    
    function filterProducts() {
        // Categoria selezionata
        const selectedCategory = document.querySelector('input[name="category"]:checked')?.value || 'all';
        
        // Ingredienti selezionati
        const selectedIngredients = Array.from(ingredientInputs)
            .filter(i => i.checked)
            .map(i => i.value.toLowerCase());
        
        // Prezzo selezionato
        const selectedPrice = document.querySelector('input[name="priceRange"]:checked')?.value || 'all';
        
        // Base selezionata
        const selectedBase = document.querySelector('input[name="baseFilter"]:checked')?.value || 'all';
        
        let visibleCount = 0;
        
        cards.forEach(card => {
            const prodCat = card.dataset.category;
            const prodIngs = card.dataset.ingredients || '';
            const prodPrice = parseFloat(card.dataset.price) || 0;
            const prodBase = card.dataset.base || '';
            
            // Verifica Categoria
            const catMatch = selectedCategory === 'all' || selectedCategory === prodCat;
            
            // Verifica Ingredienti
            let ingMatch = true;
            if (selectedIngredients.length > 0) {
                ingMatch = selectedIngredients.some(ing => 
                    prodIngs.toLowerCase().includes(ing.toLowerCase())
                );
            }
            
            // Verifica Prezzo
            let priceMatch = true;
            if (selectedPrice !== 'all') {
                if (selectedPrice === 'low') priceMatch = prodPrice <= 5;
                else if (selectedPrice === 'medium') priceMatch = prodPrice > 5 && prodPrice <= 10;
                else if (selectedPrice === 'high') priceMatch = prodPrice > 10;
            }
            
            // Verifica Base
            let baseMatch = true;
            if (selectedBase !== 'all') {
                baseMatch = prodBase.toLowerCase().includes(selectedBase.toLowerCase());
            }
            
            // Mostra/Nascondi
            if (catMatch && ingMatch && priceMatch && baseMatch) {
                card.style.display = 'flex';
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });
        
        // Mostra messaggio se 0 risultati
        if (noResults) {
            noResults.style.display = (visibleCount === 0) ? 'block' : 'none';
        }
        
        // Aggiorna pulsanti filtri desktop
        updateDesktopFilters(selectedCategory);
    }
    
    // Funzione per aggiornare i pulsanti filtri desktop
    function updateDesktopFilters(selectedCategory) {
        const desktopFilterBtns = document.querySelectorAll('.catalog-filters .filter-btn');
        desktopFilterBtns.forEach(btn => {
            btn.classList.remove('active');
            if (btn.dataset.filter === selectedCategory || 
                (selectedCategory === 'all' && btn.dataset.filter === 'all')) {
                btn.classList.add('active');
            }
        });
    }
    
    // Listener per filtri
    categoryInputs.forEach(input => input.addEventListener('change', filterProducts));
    ingredientInputs.forEach(input => input.addEventListener('change', filterProducts));
    priceInputs.forEach(input => input.addEventListener('change', filterProducts));
    baseInputs.forEach(input => input.addEventListener('change', filterProducts));
    
    // Filtri desktop (pulsanti)
    document.querySelectorAll('.catalog-filters .filter-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const filter = this.dataset.filter;
            
            // Seleziona il radio button corrispondente
            const radio = document.querySelector(`input[name="category"][value="${filter}"]`);
            if (radio) {
                radio.checked = true;
                
                // Deseleziona tutti gli altri radio
                categoryInputs.forEach(input => {
                    if (input !== radio) {
                        input.checked = false;
                    }
                });
                
                filterProducts();
            }
        });
    });
    
    // 3. ORDINAMENTO (SENZA RIORDINARE IL DOM)
    if (sortSelect) {
        sortSelect.addEventListener('change', () => {
            const sortValue = sortSelect.value;
            
            // Ottieni le card visibili
            const visibleCards = cards.filter(card => card.style.display !== 'none');
            
            // Ordina virtualmente senza modificare il DOM
            let sortedCards = [...visibleCards];
            
            if (sortValue === 'priceAsc') {
                sortedCards.sort((a, b) => parseFloat(a.dataset.price) - parseFloat(b.dataset.price));
            } else if (sortValue === 'priceDesc') {
                sortedCards.sort((a, b) => parseFloat(b.dataset.price) - parseFloat(a.dataset.price));
            } else if (sortValue === 'nameAsc') {
                sortedCards.sort((a, b) => a.dataset.name.localeCompare(b.dataset.name));
            } else if (sortValue === 'nameDesc') {
                sortedCards.sort((a, b) => b.dataset.name.localeCompare(a.dataset.name));
            } else {
                return; // Rilevanza - non fare nulla
            }
            
            // Trova il container padre
            const container = document.querySelector('.products-grid.catalog-grid');
            if (!container) return;
            
            // Salva le posizioni originali
            const allCards = [...cards];
            
            // Nascondi tutte le card temporaneamente
            allCards.forEach(card => card.style.display = 'none');
            
            // Mostra le card ordinate
            sortedCards.forEach(card => {
                card.style.display = 'flex';
            });
            
            // Ri-appendi nell'ordine corretto
            // Prima tutte le card ordinate
            sortedCards.forEach(card => {
                container.appendChild(card);
            });
            
            // Poi le card nascoste (non visibili)
            allCards.filter(card => !visibleCards.includes(card)).forEach(card => {
                container.appendChild(card);
            });
        });
    }
    
    // Applica filtri iniziali
    filterProducts();
    
});