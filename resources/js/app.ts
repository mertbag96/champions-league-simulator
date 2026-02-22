import { library } from '@fortawesome/fontawesome-svg-core';
import {
    faCirclePlus,
    faCircleLeft,
    faCircleCheck,
    faCircleXmark,
    faPenToSquare,
    faTrashCan,
    faCircleQuestion,
} from '@fortawesome/free-solid-svg-icons';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import type { DefineComponent } from 'vue';
import { createApp, h } from 'vue';
import { Toaster, toast } from 'vue-sonner';
import 'vue-sonner/style.css';
import '../css/app.css';

const appName = import.meta.env.VITE_APP_NAME || 'Champions League Simulator';

library.add(
    faCirclePlus,
    faCircleLeft,
    faCircleCheck,
    faCircleXmark,
    faPenToSquare,
    faTrashCan,
    faCircleQuestion
);

createInertiaApp({
    title: (title) => (title ? `${title} - ${appName}` : appName),
    resolve: (name) => resolvePageComponent(`./pages/${name}.vue`, import.meta.glob<DefineComponent>('./pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .use(plugin, toast)
            .component('Toaster', Toaster)
            .component('font-awesome-icon', FontAwesomeIcon)
            .mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});
