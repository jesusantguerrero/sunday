<template>
    <app-layout :boards="boards">
        <div class="px-8">
            <div class="board__toolbar flex justify-between px-8 pb-24 pt-12">
                <div class="flex text-left">
                    <div class="flex justify-between mr-2">
                        <span class="text-3xl font-bold"> Accounts </span>
                    </div>
                </div>

                <div class="flex items-center">
                    <input
                        type="search"
                        class="form-input ml-2 w-48"
                        name=""
                        id=""
                        v-model="searchOptions.search"
                        placeholder="search"
                    />
                    <span class="ml-2 toolbar-buttons">
                        <i class="fa fa-user"></i>
                    </span>
                    <span class="ml-2 toolbar-buttons"
                        :class="{active: searchOptions.done}"
                        @click="toggleDone()"
                        ><i class="fa fa-eye"></i
                    ></span>
                    <span class="ml-2 toolbar-buttons">
                        <i class="fa fa-thumbtack"></i
                    ></span>
                    <span class="ml-2 toolbar-buttons">
                        <i class="fa fa-filter"></i>
                    </span>
                    <span class="ml-2 toolbar-buttons">
                        <i class="fa fa-sort"></i>
                    </span>
                </div>
            </div>

            <div class="py-12">
                <div class="max-w-8xl mx-auto sm:px-6 lg:px-8 flex">
                    <google-signin-btn label="Sign In" customClass="my-button" @click="signIn" />
                </div>
            </div>

        </div>
    </app-layout>
</template>

<script>
    import AppLayout from './../../Layouts/AppLayout'

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
        },
        data() {
            return {
                searchOptions: {}
            }
        },
        methods: {
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
