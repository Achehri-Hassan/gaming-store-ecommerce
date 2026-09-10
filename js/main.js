// js/main.js

document.addEventListener('DOMContentLoaded', () => {
    // DOM Elements
    const cartIcon = document.getElementById('cart');
    const cartContainer = document.querySelector('.carts');
    const cartCloseBtn = document.getElementById('cart-close');
    const cartBadge = document.getElementById('cart-badge');
    const cartContent = document.querySelector('.cart-content');
    const totalPriceEl = document.querySelector('.total-price');

  
    if (cartIcon && cartContainer && cartCloseBtn) {
        cartIcon.addEventListener('click', () => cartContainer.classList.add('active'));
        cartCloseBtn.addEventListener('click', () => cartContainer.classList.remove('active'));
    }

    
    fetchCart({ action: 'get' });


    document.addEventListener('click', (e) => {
        // Add to cart click from Product Card
        if (e.target.classList.contains('add-to-cart-btn')) {
            e.preventDefault();
            const productId = e.target.getAttribute('data-id');
            fetchCart({ action: 'add', product_id: productId });
            cartContainer.classList.add('active');
        }

       
        if (e.target.classList.contains('cart-remove-item')) {
            const productId = e.target.getAttribute('data-id');
            fetchCart({ action: 'remove', product_id: productId });
        }

        if (e.target.classList.contains('cart-qty-plus')) {
            const productId = e.target.getAttribute('data-id');
            const currentQty = parseInt(e.target.getAttribute('data-qty'));
            fetchCart({ 
                action: 'update_quantity', 
                product_id: productId, 
                quantity: currentQty + 1 
            });
        }

       
        if (e.target.classList.contains('cart-qty-minus')) {
            const productId = e.target.getAttribute('data-id');
            const currentQty = parseInt(e.target.getAttribute('data-qty'));
            
            fetchCart({ 
                action: 'update_quantity', 
                product_id: productId, 
                quantity: currentQty - 1 
            });
        }


        if (cartContainer.classList.contains('active')) {
          
            if (!cartContainer.contains(e.target) && 
                !cartIcon.contains(e.target) && 
                !e.target.classList.contains('add-to-cart-btn')) {
                
                cartContainer.classList.remove('active');
            }
        }
    });

 
    function escapeHtml(value) {
        return String(value ?? '').replace(/[&<>"']/g, (ch) => ({
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#39;',
        }[ch]));
    }

  
    function fetchCart(data) {
       
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

        fetch('cart-handler.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ ...data, csrf_token: csrfToken })
        })
        .then(response => response.json())
        .then(res => {
            if (res.success) {
                updateCartUI(res.cart, res.total_items, res.total_price);
            }
        })
        .catch(err => console.error('Error handling cart transaction:', err));
    }

  
    function updateCartUI(cart, totalItems, totalPrice) {
        if (cartBadge) cartBadge.textContent = totalItems;
        if (totalPriceEl) totalPriceEl.textContent = totalPrice;
        if (!cartContent) return;
        
        if (cart.length === 0) {
            cartContent.innerHTML = `
                <div class="empty-cart-msg">
                    <i class="fa-solid fa-basket-shopping"></i>
                    <p>Your cart is empty.</p>
                </div>`;
            return;
        }

    
        cartContent.innerHTML = cart.map(item => {
            const name     = escapeHtml(item.name);
            const image    = escapeHtml(item.image);
            const currency = escapeHtml(item.currency);
            const id       = escapeHtml(item.id);
            const qty      = escapeHtml(item.quantity);
            const price    = escapeHtml(item.price);

            return `
            <div class="cart-box">
                <img src="${image}" alt="${name}" />
                <div class="detail-box">
                    <div class="cart-product-title">${name}</div>
                    <div class="cart-price">${price} ${currency}</div>
                    
                    <div class="cart-quantity-controls">
                        <button class="cart-qty-btn cart-qty-minus" data-id="${id}" data-qty="${qty}">-</button>
                        <span class="cart-qty-number">${qty}</span>
                        <button class="cart-qty-btn cart-qty-plus" data-id="${id}" data-qty="${qty}">+</button>
                    </div>
                </div>
                <i class="fa-solid fa-trash cart-remove-item" data-id="${id}"></i>
            </div>
        `;
        }).join('');
    }
});