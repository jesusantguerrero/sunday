require("./bootstrap");
require("vue-multiselect/dist/vue-multiselect.min.css");

import Vue from "vue";
import { createInertiaApp } from "@inertiajs/inertia-vue";
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
window.route = route;
window.Ziggy = Ziggy;
Vue.use(Vuelidate);
Vue.use(InertiaPlugin);
Vue.use(PortalVue);
Vue.use(VueGoogleApi, config);
Vue.mixin(ConfirmModalMixin);
Vue.mixin(fireworks)
Vue.mixin({ methods: { route } });
Vue.component("multiselect", Multiselect);

createInertiaApp({
    resolve: name => require(`./Pages/${name}`),
    setup({ el, app, props, plugin }) {
        Vue.use(plugin)
        new Vue({
            render: h => h(app, props)

        }).$mount(el);
    }
})

require('./bootstrap');
