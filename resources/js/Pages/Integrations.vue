<template>
    <app-layout :boards="boards">
        <div class="">
            <div
                class="flex flex-col mx-auto max-w-8xl sm:pr-6 lg:pr-8 md:flex-row"
            >
                <div class="pt-12 w-100 md:w-full lg:w-8/12 md:mx-4">
                    <div class="flex mr-2">
                        <span class="text-3xl font-bold"> Integrations </span>
                        <!-- <button
                            class="px-5 ml-2 font-bold text-white bg-purple-400 rounded-lg btn"
                            @click="toggleAppConnection"
                        >
                            Add Connection
                        </button> -->

                        <button
                            class="px-5 ml-2 font-bold text-white bg-purple-400 rounded-lg btn"
                            @click="openAutomationModal"
                        >
                            Add Automation
                        </button>
                    </div>
                </div>
            </div>

            <div class="py-12">
                <div class="mx-auto max-w-8xl sm:px-6 lg:px-8">
                    <div class="flex w-full apps-form" >
                        <div
                            class="m-2 app-service__integration"
                            v-for="service in services"
                            @click="handleCommand(service)"
                            :key="service.id"
                        >
                            <img :src="service.logo" alt="" srcset="" width="50px" height="50px">
                            <p class="mt-2 font-bold">
                                {{ service.name }}
                            </p>
                        </div>

                            <div id="g_id_onload"
         data-client_id="587675303568-jhrj3u7gl2s8e5d47j46v1a3bi9bmits.apps.googleusercontent.com"
         data-callback="handleCredentialResponse">
    </div>
    <div class="g_id_signin" data-type="standard"></div>
                    </div>

                    <div class="w-full integrations-form">
                        <div
                            class="grid grid-cols-3 px-5 py-3 my-2 font-bold text-gray-500 bg-white cursor-pointer app-service__item"
                            v-for="service in integrations"
                            :key="service.id"
                        >
                            <div class="left">
                                <div class="head">
                                    {{ service.name }} {{ service.hash }}
                                </div>
                                <div class="tagline">
                                    {{ service.hash }} {{ service.created_at }}
                                </div>
                            </div>

                            <div class="text-right automations">
                                {{ service.automations.length }}
                            </div>

                            <div class="text-right options">
                                options
                            </div>
                        </div>
                    </div>
                </div>
            </div>


             <automation-modal
                @cancel="isAutomationModalOpen = false"
                @saved="onItemSaved"
                :boards="boards"
                type="event"
                :record-data="openedAutomation"
                :is-open="isAutomationModalOpen"
            >
            </automation-modal>
        </div>
    </app-layout>
</template>

<script>
import AppLayout from "./../Layouts/AppLayout.vue";
import AutomationModal from "../components/AutomationModal.vue";

export default {
    name: "Integrations",
    components: {
        AppLayout,
        AutomationModal
    },
    props: {
        boards: {
            type: Array,
            default() {
                return [];
            }
        },
        services: {
            type: Array,
            default() {
                return [];
            }
        },
        integrations: {
            type: Array,
            default() {
                return [];
            }
        }
    },
    data() {
        return {
            searchOptions: {},
            showAddConnection: false,
            authInstance: null,
            isAutomationModalOpen: false,
            openedAutomation: {}

        };
    },
    methods: {
        toggleAppConnection() {
            this.showAddConnection = !this.showAddConnection;
        },

        openAutomationModal(board, stage) {
            this.isAutomationModalOpen = true;
            this.openedAutomation = {
                board,
                stage
            };
        },

        onItemSaved() {
            this.$nextTick(() => {
                this.isAutomationModalOpen = false;
                this.$inertia.reload(`/integrations`, {
                    only: ["integrations"],
                    preserveState: true
                });
            });
        },

        handleCommand(service) {
            switch (service.name.toLowerCase()) {
                case "google":
                case "gmail":
                case "calendar":
                    this.google(service.name.toLowerCase(), service);
                    break;
                case "github":
                    //
                    break;
                default:
                    break;
            }
        },

        async google(scopeName, service) {
            gapi.load("auth2", () => {
               try {
                   gapi.auth2
                       .init({
                           apiKey: import.meta.env.VITE_GOOGLE_APP_KEY,
                           clientId: import.meta.env.VITE_GOOGLE_CLIENT_ID,
                           accessType: "offline",
                           scope: `profile https://www.googleapis.com/auth/gmail.readonly https://www.googleapis.com/auth/calendar.readonly https://www.googleapis.com/auth/spreadsheets.readonly`,
                           discoveryDocs: [
                               "https://www.googleapis.com/discovery/v1/apis/calendar/v3/rest",
                               "https://sheets.googleapis.com/discovery/rest?version=v4"
                           ]
                       })
                       .then(async auth => {
                           const authInstance = gapi.auth2.getAuthInstance();
                           const user = authInstance.currentUser.get();

                           if (!user.getAuthResponse().session_state) {
                               await authInstance.signIn();
                           }

                           const profile = user.getBasicProfile();

                           await authInstance
                               .grantOfflineAccess({
                                   authuser: user.getAuthResponse().session_state.extraQueryParams.authuser
                               })
                               .then(({ code }) => {
                                   const credentials = {
                                       code,
                                       service_id: service.id,
                                       service_name: service.name,
                                       user: profile.getEmail()
                                   };

                                   axios({
                                       url: "/services/google",
                                       method: "post",
                                       data: {
                                           credentials
                                       }
                                   }).then(() => {
                                       this.$inertia.reload(`/integrations`);
                                   })
                               })
                       })
               } catch (err) {
                    console.log(err)
               }
            });
        }
    }
};
</script>

<style lang="scss">
.app-service__integration {
    @apply bg-white text-gray-500 my-2 cursor-pointer px-5 py-3 font-bold;
    @apply border-2 border-gray-300 rounded-md;
    width: 150px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
}
</style>
