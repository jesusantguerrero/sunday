<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Dashboard
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-8xl mx-auto sm:px-6 lg:px-8 flex">
                <div class="w-2/12 mr-4">
                    <board-side
                        :boards="boards"
                    >
                    </board-side>
                </div>

                <div class="w-7/12 mx-2">
                    <div class="section-card committed">
                        <header class="bg-purple-400 text-white font-bold">
                            To Do
                        </header>
                        <div class="body text-gray-600">
                            <p v-for="task in todo" :key="`task-${task.id}`">
                                {{ task.title }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="w-3/12 ml-4">
                    <div class="section-card committed">
                        <header class="bg-yellow-500 text-white">
                            Comitted
                        </header>
                        <div class="body bg-yellow-200 text-gray-600">
                            Hola soy un item de ejemplo
                        </div>
                    </div>

                    <div class="section-card committed">
                        <header class="bg-green-500 text-white font-bold">
                            Events
                        </header>
                        <div class="body text-gray-600">
                            Hola soy un item de ejemplo
                        </div>
                    </div>

                    <div class="section-card committed">
                        <header class="bg-blue-400 text-white font-bold">
                            Links
                        </header>
                         <div class="body text-gray-600">
                            Hola soy un item de ejemplo
                        </div>
                    </div>

                    <div class="section-card committed">
                        <header class="bg-red-400 text-white font-bold">
                            Timer
                        </header>
                         <div class="body bg-red-400 text-gray-600 font-bold">
                            This is a timer
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </app-layout>
</template>

<script>
    import AppLayout from './../Layouts/AppLayout'
    import Welcome from './Welcome'
    import BoardSide from "../components/board/BoardSide"

    export default {
        components: {
            AppLayout,
            Welcome,
            BoardSide
        },
        props: {
            boards: {
                type: Array,
                default() {
                    return []
                }
            },
            todo: {
                type: [Array, Object],
                default() {
                    return []
                }
            }
        }
    }
</script>

<style lang="scss">
.section-card {
    @apply bg-white overflow-hidden shadow-xl mx-2 mb-4;

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
