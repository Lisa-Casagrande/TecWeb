document.addEventListener('DOMContentLoaded', function() {
    const inputs = document.querySelectorAll('#login-form input');

    inputs.forEach(input => {
        input.addEventListener('input', function() {
            // Rimuove classe errore
            input.classList.remove('input-error');

            // Rimuove il messaggio di errore associato
            const errorId = input.getAttribute('aria-describedby')?.split(' ')?.find(id => id.includes('error'));
            if (errorId) {
                const errorElem = document.getElementById(errorId);
                if (errorElem) errorElem.textContent = '';
            }
        });
    });
});
