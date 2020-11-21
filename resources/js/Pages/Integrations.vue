<template>
    <app-layout :boards="boards">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Dashboard
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-8xl mx-auto sm:px-6 lg:px-8 flex">
                <google-signin-btn label="Sign In" customClass="my-button" @click="signIn" />
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
        methods: {
            async signIn() {
                    gapi.load('auth2', () => {
                        debugger
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
