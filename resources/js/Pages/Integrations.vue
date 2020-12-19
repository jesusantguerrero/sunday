<template>
    <app-layout :boards="boards">
        <div class="">
            <div class="max-w-8xl mx-auto sm:pr-6 lg:pr-8 flex flex-col md:flex-row">
                <div class="w-100 md:w-full lg:w-8/12 md:mx-4 pt-12">
                    <div class="flex mr-2">
                        <span class="text-3xl font-bold"> Integrations </span>
                        <button
                            class="btn bg-purple-400 text-white font-bold"
                            @click="toggleAppConnection">
                            Add Connection
                        </button>
                    </div>
                </div>
            </div>

            <div class="py-12">
                <div class="max-w-8xl mx-auto sm:px-6 lg:px-8 flex">
                    <div class="apps-form">
                        <div class="app-service__item" v-for="service in services" :key="service.id">
                            {{ service.name }}
                        </div>
                    </div>
                    <google-signin-btn label="Sign In" customClass="my-button"  />
                </div>
            </div>

        </div>
    </app-layout>
</template>

<script>
    import AppLayout from './../Layouts/AppLayout'

    export default {
        name: "Integrations",
        components: {
            AppLayout
        },
        props: {
            boards: {
                type: Array,
                default() {
                    return []
                }
            },
            services: {
                type: Array,
                default() {
                    return []
                }
            },
            integrations: {
                type: Array,
                default() {
                    return []
                }
            },
        },
        data() {
            return {
                searchOptions: {},
                showAddConnection: false
            }
        },
        methods: {
            toggleAppConnection() {
                this.showAddConnection = !this.showAddConnection;
            },

            onItemSaved() {
                this.$nextTick(() => {
                    this.isItemModalOpen = false
                    this.$inertia.reload(`/planner${this.params}`, {
                        only: ["scheduled"],
                        preserveState: true
                    });
                })
            },

            async signIn() {
                    gapi.load('auth2', () => {
                        gapi.auth2.init({
                            apiKey: process.env.MIX_GOOGLE_APP_KEY,
                            clientId: process.env.MIX_GOOGLE_CLIENT_ID,
                            accessType: 'offline',
                            scope: 'profile https://www.googleapis.com/auth/gmail.readonly https://www.googleapis.com/auth/calendar.readonly',
                            discoveryDocs: ["https://www.googleapis.com/discovery/v1/apis/calendar/v3/rest"]
                        }).then((auth) => {
                                const authInstance = gapi.auth2.getAuthInstance();
                                const user = authInstance.currentUser.get();
                                authInstance.grantOfflineAccess({
                                    authuser: user.getAuthResponse().session_state.extraQueryParams.authuser
                                }).then(({ code }) => {
                                    const credentials = { code };
                                    axios({
                                        url: '/services/google',
                                        method: 'post',
                                        data: credentials
                                    }).catch(() => {
                                        console.log(e)
                                    })
                                })
                        })


                })
            }
        }
    }
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
</style>
