import './bootstrap';
import '../css/app.css';

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy/dist/vue.m';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});

// Boton para cambiar modo
// Verificar si el navegador está en modo oscuro mediante matchMedia
if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
    // El navegador está en modo oscuro
    document.querySelectorAll('.base_swith').forEach(function(element) {
        element.classList.add('bg-green-500');
        element.classList.remove('bg-slate-700');
    });
    document.querySelectorAll('.circulo_swith').forEach(function(element) {
        element.classList.add('prendido');
    });
    document.documentElement.classList.add('dark');
} else {
    // El navegador no está en modo oscuro
    document.querySelectorAll('.base_swith').forEach(function(element) {
        element.classList.remove('bg-green-500');
        element.classList.add('bg-slate-700');
    });
    document.querySelectorAll('.circulo_swith').forEach(function(element) {
        element.classList.remove('prendido');
    });
    document.documentElement.classList.remove('dark');
}

// Verificar si el modo oscuro está activado en localStorage
if (localStorage.getItem('modoOscuro') === 'true') {
    document.documentElement.classList.add('dark');
    document.querySelectorAll('.base_swith').forEach(function(element) {
        element.classList.add('bg-green-500');
        element.classList.remove('bg-slate-700');
    });
    document.querySelectorAll('.circulo_swith').forEach(function(element) {
        element.classList.add('prendido');
    });
} else {
    document.documentElement.classList.remove('dark');
    document.querySelectorAll('.base_swith').forEach(function(element) {
        element.classList.remove('bg-green-500');
        element.classList.add('bg-slate-700');
    });
    document.querySelectorAll('.circulo_swith').forEach(function(element) {
        element.classList.remove('prendido');
    });
}

// Manejar el evento de clic para alternar el modo oscuro
document.getElementById('swith_modo_oscuro').addEventListener('click', function() {
    document.querySelectorAll('.base_swith').forEach(function(element) {
        element.classList.toggle('bg-green-500');
        element.classList.toggle('bg-slate-700');
    });
    document.querySelectorAll('.circulo_swith').forEach(function(element) {
        element.classList.toggle('prendido');
    });
    document.documentElement.classList.toggle('dark');
    localStorage.setItem('modoOscuro', document.documentElement.classList.contains('dark'));
});
// Boton para cambiar modo