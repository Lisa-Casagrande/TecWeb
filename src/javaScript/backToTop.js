/* ===================================
   PULSANTE TORNA SU
   =================================== */

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
    
    /**
     * Mostra/nascondi pulsante in base allo scroll
     */
    function toggleButtonVisibility() {
        const scrollY = window.scrollY || window.pageYOffset;
        
        if (scrollY > SCROLL_THRESHOLD) {
            backToTopButton.classList.add('visible');
        } else {
            backToTopButton.classList.remove('visible');
        }
    }
    
    /**
     * Scroll fluido verso l'alto
     */
    function scrollToTop(event) {
        event.preventDefault();
        
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
        
        // ACCESSIBILITÀ: Focus sul skip link dopo scroll
        // (migliora navigazione tastiera)
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