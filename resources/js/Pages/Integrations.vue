<template>
    <app-layout :boards="boards">
        <div class="">
            <div
                class="max-w-8xl mx-auto sm:pr-6 lg:pr-8 flex flex-col md:flex-row"
            >
                <div class="w-100 md:w-full lg:w-8/12 md:mx-4 pt-12">
                    <div class="flex mr-2">
                        <span class="text-3xl font-bold"> Integrations </span>
                        <!-- <button
                            class="btn bg-purple-400 text-white font-bold ml-2 rounded-lg px-5"
                            @click="toggleAppConnection"
                        >
                            Add Connection
                        </button> -->

                        <button
                            class="btn bg-purple-400 text-white font-bold ml-2 rounded-lg px-5"
                            @click="openAutomationModal"
                        >
                            Add Automation
                        </button>
                    </div>
                </div>
            </div>

            <div class="py-12">
                <div class="max-w-8xl mx-auto sm:px-6 lg:px-8">
                    <div class="apps-form w-full flex" >
                        <div
                            class="app-service__integration m-2"
                            v-for="service in services"
                            @click="handleCommand(service)"
                            :key="service.id"
                        >
                            <img :src="service.logo" alt="" srcset="" width="50px" height="50px">
                            <p class="mt-2 font-bold">
                                {{ service.name }}
                            </p>
                        </div>
                    </div>

                    <div class="integrations-form w-full">
                        <div
                            class="app-service__item bg-white text-gray-500 my-2 cursor-pointer px-5 py-3 font-bold grid grid-cols-3"
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

                            <div class="automations text-right">
                                {{ service.automations.length }}
                            </div>

                            <div class="options text-right">
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
import AppLayout from "./../Layouts/AppLayout";
import AutomationModal from "../components/AutomationModal";

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
                gapi.auth2
                    .init({
                        apiKey: process.env.MIX_GOOGLE_APP_KEY,
                        clientId: process.env.MIX_GOOGLE_CLIENT_ID,
                        accessType: "offline",
                        scope: `profile https://www.googleapis.com/auth/gmail.readonly https://www.googleapis.com/auth/calendar.readonly`,
                        discoveryDocs: ["https://www.googleapis.com/discovery/v1/apis/calendar/v3/rest"]
                    })
                    .then(async auth => {
                        const authInstance = gapi.auth2.getAuthInstance();
                        const user = authInstance.currentUser.get();

                        if (!user.getAuthResponse().session_state) {
                            await authInstance.signIn();
                        }

                        await authInstance
                            .grantOfflineAccess({
                                authuser: user.getAuthResponse().session_state.extraQueryParams.authuser
                            })
                            .then(({ code }) => {
                                const credentials = {
                                    code,
                                    service_id: service.id,
                                    service_name: service.name,
                                    user
                                };

                                axios({
                                    url: "/services/google",
                                    method: "post",
                                    data: {
                                        credentials
                                    }
                                })
                            })
                    })
            });
        }
    }
};
</script>

<style lang="scss">
.section-card {
    @apply bg-white overflow-hidden shadow-xl mx-2 mb-4;
    &.margin-0 {
        @apply m-0;
    }

    header {
        @apply p-4;
    }

    .body {
        @apply p-4;
        min-height: 5rem;
    }
}

button {
    &:focus {
        outline: 0 !important;
    }
}

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
