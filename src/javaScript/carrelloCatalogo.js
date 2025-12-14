// javaScript/carrelloCatalogo.js

document.addEventListener('DOMContentLoaded', function() {
    const cartButtons = document.querySelectorAll('.aggiungi-carrello');
    
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
            
            // Aggiorna contatore carrello (se esiste)
            updateCartCounter();
        });
    });
    
    // Funzione per aggiungere al carrello
    function addToCart(id, name, price, img) {
        let cart = JSON.parse(localStorage.getItem('carrello')) || [];
        
        // Controlla se il prodotto è già nel carrello
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
    
    // Inizializza contatore carrello al caricamento
    updateCartCounter();
});