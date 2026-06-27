/**
 * cart-csrf.js
 * Reads the CSRF token injected by PHP and attaches it
 * to every cart-handler AJAX request.
 */
const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

async function cartRequest(payload) {
    const res = await fetch('cart-handler.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ ...payload, csrf_token: CSRF_TOKEN }),
    });
    return res.json();
}
