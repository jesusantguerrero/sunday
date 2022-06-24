require("./bootstrap");
require("vue-multiselect/dist/vue-multiselect.min.css");

import Vue from "vue";
import { createInertiaApp, InertiaLink } from "@inertiajs/inertia-vue";
import ConfirmModalMixin from "./plugins/ConfirmModalMixin";
import VueGoogleApi from "vue-google-api";
import route from 'ziggy';
import { Ziggy } from './ziggy';
import "./plugins/element-ui";

const config = {
    apiKey: process.env.MIX_GOOGLE_APP_KEY,
    clientId: process.env.MIX_GOOGLE_CLIENT_ID,
    accessType: "offline",
    scope: "profile https://www.googleapis.com/auth/gmail.readonly",
    discoveryDocs: []
};

import Multiselect from "vue-multiselect";
import PortalVue from "portal-vue";
import Vuelidate from "vuelidate";
import fireworks from "./plugins/fireworks";
import { InertiaProgress } from "@inertiajs/progress";
window.route = route;
window.Ziggy = Ziggy;


createInertiaApp({
    resolve: name => require(`./Pages/${name}`),
    setup({ el, app, props, plugin }) {
        Vue.use(plugin)
        Vue.use(Vuelidate);
        Vue.use(PortalVue);
        Vue.use(VueGoogleApi, config);
        Vue.mixin(ConfirmModalMixin);
        Vue.mixin(fireworks)
        Vue.mixin({ methods: { route } });
        Vue.component("multiselect", Multiselect);
        Vue.component('inertia-progress', InertiaProgress)
        Vue.component('inertia-link', InertiaLink)
        new Vue({
            render: h => h(app, props)

        }).$mount(el);
    }
})

require('./bootstrap');
