import './bootstrap';
import '../css/app.scss';

import { createApp, h } from "vue";
import { createInertiaApp, Link  } from "@inertiajs/inertia-vue3";
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';

import { InertiaProgress } from "@inertiajs/progress";

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: name => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, app, props, plugin }) {
        createApp({ render: () => h(app, props)})
        .mixin({ methods: { route } })
        .use(plugin)
        .component('InertiaProgress', InertiaProgress)
        .component('InertiaLink', Link)
        .mount(el);
    }
})

InertiaProgress.init({ color: '#4B5563' });

