require('./bootstrap');
require('vue-multiselect/dist/vue-multiselect.min.css')

import Vue from 'vue';

import { InertiaApp } from '@inertiajs/inertia-vue';
import { InertiaForm } from 'laravel-jetstream';
import VueGoogleApi from 'vue-google-api'
import VCalendar from "v-calendar";

const config = {
  apiKey: process.env.MIX_GOOGLE_APP_KEY,
  clientId: process.env.MIX_GOOGLE_CLIENT_ID,
  accessType: 'offline',
  scope: 'profile https://www.googleapis.com/auth/gmail.readonly',
  discoveryDocs: []
}
import Multiselect from 'vue-multiselect'
import PortalVue from 'portal-vue';

Vue.use(InertiaApp);
Vue.use(InertiaForm);
Vue.use(PortalVue);
Vue.use(VCalendar);
Vue.use(VueGoogleApi, config)
Vue.component('multiselect', Multiselect)

const app = document.querySelector('[data-page]');

new Vue({
    render: (h) =>
        h(InertiaApp, {
            props: {
                initialPage: JSON.parse(app.dataset.page),
                resolveComponent: (name) => require(`./Pages/${name}`).default,
            },
        }),
}).$mount(app);
