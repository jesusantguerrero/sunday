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
