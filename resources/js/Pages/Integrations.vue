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
                            :ref="service.name"
                            :key="service.id"
                        >
                            <img :src="service.logo" alt="" :style="{width: '50px', height: '50px'}">
                            <p class="mt-2 font-bold">
                                {{ service.name }}
                            </p>
                        </div>
                    </div>

                    <div class="w-full integrations-form">
                        <div
                            class="grid grid-cols-3 px-5 py-3 my-2 font-bold text-gray-500 bg-white cursor-pointer app-service__item"
                            v-for="service in integrations"
                            :key="service.id"
                            ref="service.name"
                        >
                            <div class="left">
                                <div class="head">
                                    {{ service.name }} {{ service.hash }}
                                </div>
                                <div class="tagline text-gray-400">
                                    Rules:
                                    <span v-for="automation in service.automations">
                                        {{ automation.name }}
                                    </span>
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
            />
        </div>
    </app-layout>
</template>

<script>
import AppLayout from "./../Layouts/AppLayout.vue";
import AutomationModal from "../components/AutomationModal.vue";
import jwtDecode from "jwt-decode";
import { createElementBlock } from '@vue/runtime-core';

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

        async google() {
            const service = this.services.find(service => service.name = "Google");
            const credentials = {
                service_id: service.id,
                service_name: service.name,
            };
            axios({
                url: "/services/google",
                method: "post",
                data: {
                    credentials
                }
            }).then(({ data }) => {
                location.href = data
            })
        },
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
