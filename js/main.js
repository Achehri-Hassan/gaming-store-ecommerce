// js/main.js

document.addEventListener('DOMContentLoaded', () => {
    // DOM Elements
    const cartIcon = document.getElementById('cart');
    const cartContainer = document.querySelector('.carts');
    const cartCloseBtn = document.getElementById('cart-close');
    const cartBadge = document.getElementById('cart-badge');
    const cartContent = document.querySelector('.cart-content');
    const totalPriceEl = document.querySelector('.total-price');

    // Toggle Cart Visibility
    if (cartIcon && cartContainer && cartCloseBtn) {
        cartIcon.addEventListener('click', () => cartContainer.classList.add('active'));
        cartCloseBtn.addEventListener('click', () => cartContainer.classList.remove('active'));
    }

    // Load Cart on page load
    fetchCart({ action: 'get' });

    // Global click listener for structural delegation (Dynamic buttons)
    document.addEventListener('click', (e) => {
        // Add to cart click from Product Card
        if (e.target.classList.contains('add-to-cart-btn')) {
            e.preventDefault();
            const productId = e.target.getAttribute('data-id');
            fetchCart({ action: 'add', product_id: productId });
            cartContainer.classList.add('active');
        }

        // Remove item entirely via trash icon
        if (e.target.classList.contains('cart-remove-item')) {
            const productId = e.target.getAttribute('data-id');
            fetchCart({ action: 'remove', product_id: productId });
        }

        // 🟢 الـ Plus Button (+) وسط السلة
        if (e.target.classList.contains('cart-qty-plus')) {
            const productId = e.target.getAttribute('data-id');
            const currentQty = parseInt(e.target.getAttribute('data-qty'));
            fetchCart({ 
                action: 'update_quantity', 
                product_id: productId, 
                quantity: currentQty + 1 
            });
        }

        // 🟢 الـ Minus Button (-) وسط السلة
        if (e.target.classList.contains('cart-qty-minus')) {
            const productId = e.target.getAttribute('data-id');
            const currentQty = parseInt(e.target.getAttribute('data-qty'));
            // يلا كانت الكمية 1 وبرك على ناقص، غاتحيد من السلة
            fetchCart({ 
                action: 'update_quantity', 
                product_id: productId, 
                quantity: currentQty - 1 
            });
        }


        if (cartContainer.classList.contains('active')) {
            // يلا كليكا خارج الـ .carts و ماشي على أيقونة السلة (لي كتفتحها) و ماشي على زر add to cart
            if (!cartContainer.contains(e.target) && 
                !cartIcon.contains(e.target) && 
                !e.target.classList.contains('add-to-cart-btn')) {
                
                cartContainer.classList.remove('active');
            }
        }
    });

    // Core function to interact with PHP backend
    function fetchCart(data) {
        fetch('cart-handler.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(res => {
            if (res.success) {
                updateCartUI(res.cart, res.total_items, res.total_price);
            }
        })
        .catch(err => console.error('Error handling cart transaction:', err));
    }

    // Function to render the new state into HTML dynamically
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

        // Render with dynamic plus/minus controls
        cartContent.innerHTML = cart.map(item => `
            <div class="cart-box">
                <img src="${item.image}" alt="${item.name}" />
                <div class="detail-box">
                    <div class="cart-product-title">${item.name}</div>
                    <div class="cart-price">${item.price} ${item.currency}</div>
                    
                    <div class="cart-quantity-controls">
                        <button class="cart-qty-btn cart-qty-minus" data-id="${item.id}" data-qty="${item.quantity}">-</button>
                        <span class="cart-qty-number">${item.quantity}</span>
                        <button class="cart-qty-btn cart-qty-plus" data-id="${item.id}" data-qty="${item.quantity}">+</button>
                    </div>
                </div>
                <i class="fa-solid fa-trash cart-remove-item" data-id="${item.id}"></i>
            </div>
        `).join('');
    }
});