require("./bootstrap");
require("vue-multiselect/dist/vue-multiselect.min.css");

import { ZiggyVue } from '../../vendor/tightenco/ziggy/dist/vue';
import Vue from "vue";
import { App as InertiaApp, plugin as InertiaPlugin } from "@inertiajs/inertia-vue";
import ConfirmModalMixin from "./plugins/ConfirmModalMixin";
import VueGoogleApi from "vue-google-api";
import route from 'ziggy';
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
Vue.use(Vuelidate);
Vue.use(InertiaPlugin);
Vue.use(PortalVue);
Vue.use(VueGoogleApi, config);
Vue.mixin(ConfirmModalMixin);
Vue.mixin(fireworks)
Vue.mixin({ methods: { route } });
Vue.component("multiselect", Multiselect);

const app = document.querySelector("[data-page]");

new Vue({
    render: h =>
        h(InertiaApp, {
            props: {
                initialPage: JSON.parse(app.dataset.page),
                resolveComponent: name => require(`./Pages/${name}`).default
            }
        })
}).$mount(app);

require('./bootstrap');
