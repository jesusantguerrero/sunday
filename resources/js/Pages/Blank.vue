<template>
    <app-layout :boards="boards">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Dashboard
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-8xl mx-auto sm:px-6 lg:px-8 flex">
               Comming Soon
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
                    this.$gapi.signIn().then(async() => {
                    const baseGapi = await this.$gapi._load();
                    const authInstance = baseGapi.auth2.getAuthInstance();
                    const user = authInstance.currentUser.get();

                    authInstance.grantOfflineAccess({
                        authuser: user.getAuthResponse().session_state.extraQueryParams.authuser
                    }).then(({ code }) => {
                        const credentials = { code };
                        axios({
                            url: 'services/google',
                            method: 'post',
                            data: credentials
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
