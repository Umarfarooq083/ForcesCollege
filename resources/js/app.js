import '../css/app.css';
import './bootstrap';

import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createApp, h } from 'vue';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import Toast from 'vue-toastification';
import 'vue-toastification/dist/index.css';
import { initTooltips } from './Utils/tooltip';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

// createInertiaApp({
//     title: (title) => `${title} - ${appName}`,
//     resolve: (name) =>
//         resolvePageComponent(
//             `./Pages/${name}.vue`,
//             import.meta.glob('./Pages/**/*.vue'),
//         ),
//     setup({ el, App, props, plugin }) {
//         return createApp({ render: () => h(App, props) })
//             .use(plugin)
//             .use(ZiggyVue)
//             .use(Toast, {
//                 transition: 'Vue-Toastification__bounce',
//                 maxToasts: 3,
//                 newestOnTop: true,
//                 position: 'top-right',
//             })
//             .mount(el);
//     },
//     progress: {
//         color: '#59C4BC',
//     },
// });



createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob('./Pages/**/*.vue'),
        ),
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .use(Toast, {
                transition: 'Vue-Toastification__bounce',
                maxToasts: 3,
                newestOnTop: true,
                position: 'top-right',
            });

        app.mixin({
            mounted() {
                initTooltips();
            },
            updated() {
                initTooltips();
            },
        });

        app.mount(el);
    },
    progress: {
        color: '#59C4BC',
    },
});
