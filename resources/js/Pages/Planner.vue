<template>
    <app-layout :boards="boards">
        <div class="">
            <div class="max-w-8xl mx-auto sm:pr-6 lg:pr-8 flex flex-col md:flex-row">
                <!-- Main board -->
                <div class="w-100 md:w-9/12 md:mx-4 pt-12">
                    <div class="flex justify-between flex-col md:flex-row mx-2 md:mr-2 md:ml-6">
                        <span class="text-3xl font-bold"> Planner </span>
                        <div class="controls h-12 bg-purple-700 rounded-lg">
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

                    <div class="mt-5 md:ml-6">
                        <schedule-view
                            v-model="localDate"
                            :schedule="scheduled"
                            id-field=""
                            time-field=""
                            date-field=""
                            time-end-field=""
                            date-end-field=""
                            title-field=""
                        >

                        </schedule-view>
                    </div>
                </div>
                <!-- End of main board -->

                <!-- Right Side -->
                <div class="w-100 md:w-3/12 md:ml-4 pt-12">
                    <span class="text-3xl ml-2 font-bold"> Fast Access </span>

                    <div class="section-card committed mt-5">
                        <header class="bg-blue-400 text-white font-bold flex justify-between">
                            <span>
                                Priorities
                            </span>
                            <button class="bg-transparent text-white" @click="isLinkFormOpen = !isLinkFormOpen">
                                <i class="fa fa-plus"></i>
                            </button>
                        </header>
                         <div class="body text-gray-600">

                        </div>
                    </div>
                    <div class="section-card committed mt-5">
                        <header class="bg-blue-500 text-white font-bold flex justify-between">
                            <span>
                                Workout
                            </span>
                            <button class="bg-transparent text-white" @click="isLinkFormOpen = !isLinkFormOpen">
                                <i class="fa fa-plus"></i>
                            </button>
                        </header>
                         <div class="body text-gray-600">

                        </div>
                    </div>
                    <div class="section-card committed mt-5">
                        <header class="bg-blue-600 text-white font-bold flex justify-between">
                            <span>
                                Habits
                            </span>
                            <button class="bg-transparent text-white" @click="isLinkFormOpen = !isLinkFormOpen">
                                <i class="fa fa-plus"></i>
                            </button>
                        </header>
                         <div class="body text-gray-600">


                        </div>
                    </div>
                    <div class="section-card committed mt-5">
                        <header class="bg-blue-700 text-white font-bold flex justify-between">
                            <span>
                                Notes
                            </span>
                            <button class="bg-transparent text-white" @click="isLinkFormOpen = !isLinkFormOpen">
                                <i class="fa fa-plus"></i>
                            </button>
                        </header>
                         <div class="body text-gray-600">

                        </div>
                    </div>


                </div>
                <!-- End of Right Side -->
            </div>
        </div>
    </app-layout>
</template>

<script>
    import AppLayout from './../Layouts/AppLayout'
    import BoardSide from "../components/board/BoardSide"
    import BoardItemContainer from "../components/board/ItemContainer"
    import ScheduleControls from "../components/schedule/controls";
    import ScheduleView from "../components/schedule"
    import Promodoro from "../components/promodoro/index"
    import DialogModal from "../Jetstream/DialogModal"
    import LinkFormModal from "../components/links/Form"
    import LinkViewer from "../components/links/Viewer"
    import PrimaryButton from "../Jetstream/Button"
    import { subDays, toDate } from "date-fns";

    export default {
        components: {
            AppLayout,
            BoardSide,
            BoardItemContainer,
            ScheduleView,
            Promodoro,
            DialogModal,
            LinkFormModal,
            LinkViewer,
            PrimaryButton,
            ScheduleControls
        },
        props: {
            boards: {
                type: Array,
                default() {
                    return []
                }
            },
            scheduled: {
                type: [Array, Object],
                default() {
                    return []
                }
            },
            date: {
                type: String
            },
        },
        data() {
            return {
                modes: ['daily', 'weekly', 'monthly', 'quarter'],
                modeSelected: 'daily',
                promodoroColor: "red",
                localDate: null,
                isLoading: false,
                isStandupOpen: false,
                isLinkFormOpen: false,
                linkData: {},
                tracker: null
            }
        },
        watch: {
            localDate: {
                handler(newDate, oldDate) {
                    if (this.date && newDate &&  (newDate.toISOString().slice(0, 10) != this.date)) {
                        debugger
                        console.log("searching", newDate)
                        this.getCommitsByDate();
                    }
                },
                immediate: true
            },
            date: {
                handler(date) {
                    this.setCommitDate();
                },
                immediate: true
            }
        },
        computed: {
            hasCommited() {
                return this.todo.filter(item => item.done).length;
            },
            showTodo() {
                return ['all', 'todos'].includes(this.modeSelected)
            },
            showCommitted() {
                return ['all', 'committed'].includes(this.modeSelected)
            }
        },
        methods: {
            setCommitDate() {
                let date = null
                date = this.date.split("-");
                date = new Date(date[0], date[1] - 1, date[2]);
                this.localDate = date;
            },

            getCommitsByDate() {
                const params = `?date=${this.localDate.toISOString().slice(0, 10)}`
                this.$inertia.replace(`/planner${params}`,
                 {
                    only: ['scheduled', 'date'],
                    preserveState: true
                 })
            },
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

    .body.p-0 {
        @apply p-0;
    }
}

button {
    &:focus {
        outline: 0 !important;
    }
}
</style>
