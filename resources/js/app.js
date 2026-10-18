import './bootstrap';
import Alpine from 'alpinejs';

// Alpine drives the sidebar, dropdowns and small toggles, as in jhs_portal (bundled here instead of a CDN).
window.Alpine = Alpine;
Alpine.start();

// Show / hide password toggles (from jhs_portal's auth-split layout).
document.querySelectorAll('[data-toggle-password]').forEach((btn) => {
    btn.addEventListener('click', () => {
        const input = document.getElementById(btn.dataset.togglePassword);
        const show = input.type === 'password';
        input.type = show ? 'text' : 'password';
        btn.querySelector('i').className = show ? 'fas fa-eye-slash' : 'fas fa-eye';
        btn.setAttribute('aria-label', show ? 'Hide password' : 'Show password');
    });
});

// Prevent double submits on forms marked data-auth-form and show progress.
document.querySelectorAll('form[data-auth-form]').forEach((form) => {
    form.addEventListener('submit', () => {
        const button = form.querySelector('button[type=submit]');
        if (button) {
            button.disabled = true;
            button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Please wait…';
        }
    });
});
