import Alpine from 'alpinejs';

window.Alpine = Alpine;

window.confirmDialog = function(detail) {
    window.dispatchEvent(new CustomEvent('confirm-dialog', { detail: detail || {} }));
};

Alpine.start();
