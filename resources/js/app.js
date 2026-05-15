import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

//Estado de carga
document.addEventListener('DOMContentLoaded', () => {
    const btn = document.getElementById('btn-login');

    if (btn) {
        btn.addEventListener('click', () => {
            btn.style.pointerEvents = 'none';
            btn.style.opacity = '0.75';
            document.getElementById('btn-icon').className = 'ti ti-loader-2 animate-spin';
            document.getElementById('btn-texto').textContent = 'Cargando...';
        });
    }
});
