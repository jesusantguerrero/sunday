import './bootstrap';
import '../css/app.scss';
import "atmosphere-ui/style.css"
import "vue-multiselect/dist/vue-multiselect.css"

import { createApp, h } from "vue";
import { createInertiaApp, Link  } from "@inertiajs/inertia-vue3";
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import VueMultiselect from 'vue-multiselect'
import { InertiaProgress } from "@inertiajs/progress";
import { autoAnimatePlugin } from '@formkit/auto-animate/vue'
import FloatingVue from 'floating-vue'

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: name => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, app, props, plugin }) {
        createApp({ render: () => h(app, props)})
        .mixin({ methods: { route } })
        .use(plugin)
        .use(autoAnimatePlugin)
        .use(FloatingVue)
        .component('InertiaProgress', InertiaProgress)
        .component('InertiaLink', Link)
        .component('Multiselect', VueMultiselect)
        .mount(el);
    }
})

InertiaProgress.init({ color: '#4B5563' });

