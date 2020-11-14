<template>
    <app-layout :boards="boards">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Dashboard
            </h2>
        </template>

        <div class="">
            <div class="max-w-8xl mx-auto flex">
                <time-entry-form
                    :current="current"
                    @stopped="reloadTracks"
                >
                </time-entry-form>
            </div>
            <div class="mt-10 items-container">
                <div class="items-container__header flex justify-between px-8 mb-10">
                        <span class="text-3xl font-bold"> Time entries </span>
                        <div class="controls bg-purple-700 rounded-lg">
                            <button
                                v-for="mode in modes"
                                :key="mode"
                                @click="modeSelected=mode"
                                :class="{'bg-purple-400': mode == modeSelected }"
                                class="px-8 h-full rounded-lg text-white capitalize">
                                    {{ mode }}
                            </button>
                        </div>
                </div>
                <div class="items-container__list px-8">
                    <time-entry-item :time-entry="track" v-for="track in tracks" :key="track.id">
                    </time-entry-item>
                </div>
            </div>
        </div>
    </app-layout>
</template>

<script>
    import AppLayout from './../Layouts/AppLayout'
    import TimeEntryForm from "../components/timeTracker/Form"
    import TimeEntryItem from "../components/timeTracker/Item"

    export default {
        name: "Integrations",
        components: {
            AppLayout,
            TimeEntryForm,
            TimeEntryItem
        },
        props:{
            current: {
                type: Array,
                default() {
                    return []
                }
            },
            tracks: {
                type: Array,
                default() {
                    return []
                }
            },
            boards: {
                type: Array,
                default() {
                    return []
                }
            }
        },
        created() {
             this.$parent.$on('session::stopped', () => {
                 this.$nextTick(() => {
                     this.reloadTracks()
                 })
             })
        },
        data() {
            return {
                modes: ['list', 'grid'],
                modeSelected: 'list',
            }
        },
        methods: {
            reloadTracks() {
                 this.$inertia.reload({
                    preserveScroll: true
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
