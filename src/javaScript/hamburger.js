/* ===================================
   Hamburgher per nav
   =================================== */

document.addEventListener('DOMContentLoaded', function() {
    console.log('Script hamburger caricato!');
    
    const hamburger = document.getElementById('hamburger');
    const nav = document.querySelector('nav');
    const headerUtilities = document.querySelector('.header-utilities');
    const body = document.body;
    
    // Debug per vedere se trova gli elementi
    console.log('Hamburger trovato:', hamburger);
    console.log('Nav trovata:', nav);
    console.log('Utilities trovate:', headerUtilities);
    
    if (!hamburger || !nav || !headerUtilities) {
        console.error('Elementi non trovati! Controlla i selettori.');
        return;
    }
    
    // Funzione per gestire il tema nel menu mobile
    function setupMobileThemeToggle() {
        const mobileThemeToggle = document.querySelector('.mobile-menu-wrapper .theme-toggle');
        if (mobileThemeToggle) {
            // Rimuovi eventuali listener esistenti
            mobileThemeToggle.replaceWith(mobileThemeToggle.cloneNode(true));
            const newToggle = document.querySelector('.mobile-menu-wrapper .theme-toggle');
            
            newToggle.addEventListener('click', function() {
                console.log('Toggle tema cliccato nel menu mobile');
                const isDark = document.body.classList.toggle('dark-theme');
                localStorage.setItem('theme', isDark ? 'dark' : 'light');
                
                // Aggiorna anche il toggle nel header principale per consistenza
                const mainThemeToggle = document.querySelector('.header-utilities .theme-toggle');
                if (mainThemeToggle) {
                    // Il CSS si occuperà di mostrare/nascondere le icone appropriate
                }
            });
        }
    }
    
    // Crea wrapper per menu mobile (solo se non esiste già)
    let mobileMenuWrapper = document.querySelector('.mobile-menu-wrapper');
    if (!mobileMenuWrapper) {
        mobileMenuWrapper = document.createElement('div');
        mobileMenuWrapper.className = 'mobile-menu-wrapper';
        mobileMenuWrapper.style.display = 'none';
        
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
        
        // Aggiungi al body
        body.appendChild(mobileMenuWrapper);
        
        // Aggiungi event listener al pulsante di chiusura
        closeButton.addEventListener('click', closeMenu);
        
        // Setup tema per mobile
        setupMobileThemeToggle();
        
        console.log('Wrapper creato con pulsante di chiusura:', mobileMenuWrapper);
    }
    
    // Toggle menu al click hamburger
    hamburger.addEventListener('click', function(e) {
        e.stopPropagation();
        console.log('Hamburger cliccato! Stato attuale:', hamburger.classList.contains('active'));
        
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
        mobileMenuWrapper.style.display = 'flex';
        body.style.overflow = 'hidden';
        
        // Riapplica il setup del tema (importante per quando il menu viene riaperto)
        setTimeout(() => {
            setupMobileThemeToggle();
        }, 100);
        
        // Aggiungi listener per chiudere
        setTimeout(() => {
            document.addEventListener('click', handleClickOutside);
            mobileMenuWrapper.addEventListener('click', handleMenuClick);
        }, 100);
    }
    
    function closeMenu() {
        console.log('Chiusura menu...');
        hamburger.classList.remove('active');
        mobileMenuWrapper.style.display = 'none';
        body.style.overflow = '';
        document.removeEventListener('click', handleClickOutside);
        mobileMenuWrapper.removeEventListener('click', handleMenuClick);
    }
    
    function handleClickOutside(e) {
        if (!mobileMenuWrapper.contains(e.target) && !hamburger.contains(e.target)) {
            closeMenu();
        }
    }
    
    function handleMenuClick(e) {
        // Chiudi se clicchi su un link o fuori dal contenuto
        if (e.target.tagName === 'A' || e.target === mobileMenuWrapper) {
            closeMenu();
        }
    }
    
    // Chiudi con ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && hamburger.classList.contains('active')) {
            closeMenu();
        }
    });
    
    // Chiudi se ridimensioni a desktop
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
    
    console.log('Event listeners hamburger aggiunti!');
});