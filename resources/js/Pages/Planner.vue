<template>
    <app-layout :boards="boards">
        <div class="">
            <div
                class="flex flex-col mx-auto max-w-8xl sm:pr-6 lg:pr-8 md:flex-row"
            >
                <!-- Main board -->
                <div class="pt-12 w-100 md:w-full md:mx-4">
                    <div
                        class="flex flex-col justify-between mx-2 md:flex-row md:mr-2 md:ml-6"
                    >
                        <div>
                            <span class="text-3xl font-bold">
                                Planner
                            </span>
                            <button class="font-bold text-white bg-purple-400 btn" @click="openItem()">
                                Add Event
                            </button>
                        </div>
                        <!-- <div class="h-12 bg-purple-700 rounded-lg controls">
                            <button
                                v-for="mode in modes"
                                :key="mode"
                                @click="modeSelected = mode"
                                :class="{
                                    'bg-purple-400': mode == modeSelected
                                }"
                                class="h-full px-8 text-white capitalize rounded-lg"
                            >
                                {{ mode }}
                            </button>
                        </div> -->
                    </div>

                    <div class="mt-5 md:ml-6">
                        <schedule-view
                            :value="localDate"
                            @input="getCommitsByDate"
                            :modes="modes"
                            :schedule="scheduled"
                            :link-fields="{
                                url_id: 'Join',
                                url_subject: 'See meet'
                            }"
                            id-field=""
                            time-field=""
                            date-field="due_date"
                            time-end-field=""
                            date-end-field=""
                            title-field=""
                        >
                        </schedule-view>
                    </div>
                </div>
                <!-- End of main board -->

                <!-- Right Side -->
                <div class="pt-12 w-100 hidden">
                    <span class="ml-2 text-3xl font-bold"> Fast Access </span>

                    <div class="mt-5 section-card committed">
                        <header
                            class="flex justify-between font-bold text-white bg-blue-400"
                        >
                            <span>
                                Dailies
                            </span>
                            <button
                                class="text-white bg-transparent"
                                @click="openItem({}, 'daily')"
                            >
                                <i class="fa fa-plus"></i>
                            </button>
                        </header>
                        <div class="text-gray-600 bg-blue-400 body"></div>
                    </div>
                    <div class="mt-5 section-card committed">
                        <header
                            class="flex justify-between font-bold text-white bg-blue-400"
                        >
                            <span>
                                Habits
                            </span>
                            <button
                                class="text-white bg-transparent"
                                @click="openItem({}, 'habit')"
                            >
                                <i class="fa fa-plus"></i>
                            </button>
                        </header>
                        <div class="text-gray-600 bg-blue-400 body"></div>
                    </div>
                    <div class="mt-5 section-card committed">
                        <header
                            class="flex justify-between font-bold text-white bg-blue-400"
                        >
                            <span>
                                Notes
                            </span>
                            <button
                                class="text-white bg-transparent"
                                @click="isLinkFormOpen = !isLinkFormOpen"
                            >
                                <i class="fa fa-plus"></i>
                            </button>
                        </header>
                        <div class="text-gray-600 bg-blue-400 body"></div>
                    </div>
                </div>
                <!-- End of Right Side -->
            </div>

            <item-modal
                @cancel="isItemModalOpen = false"
                @saved="onItemSaved"
                :boards="boards"
                :type="boardType"
                :record-data="openedItem"
                :is-open="isItemModalOpen"
            >
            </item-modal>
        </div>
    </app-layout>
</template>

<script>
import AppLayout from "./../Layouts/AppLayout";
import BoardSide from "../components/board/BoardSide";
import ScheduleControls from "../components/schedule/controls";
import ScheduleView from "../components/schedule";
import ItemModal from "../components/board/ItemModal";
import { format } from "date-fns";

export default {
    components: {
        AppLayout,
        BoardSide,
        ScheduleView,
        ScheduleControls,
        ItemModal
    },
    props: {
        boards: {
            type: Array,
            default() {
                return [];
            }
        },
        users: {
            type: Array,
            default() {
                return [];
            }
        },
        scheduled: {
            type: [Array, Object],
            default() {
                return [];
            }
        },
        date: {
            type: String
        },
        boardTypes: {
                type: Array,
                default() {
                    return []
                }
        },
        boardTemplates: {
                type: Array,
                default() {
                    return []
                }
        },
    },
    provide() {
        return {
            users: this.users
        };
    },
    data() {
        return {
            modes: ["daily", "weekly", "monthly", "quarter"],
            modeSelected: "daily",
            promodoroColor: "red",
            boardType: "",
            isLoading: false,
            openedItem: {},
            isItemModalOpen: false
        };
    },
    computed: {
        localDate() {
            return new Date(this.date);
        }
    },
    methods: {
        setCommitDate() {
            let date = null;
            date = this.date.split("-");
            date = new Date(this.date);
            this.localDate = date;
        },

        getParams(date) {
            return `?date=${format(date, 'yyyy-MM-dd')}`;
        },

        getCommitsByDate(date) {
            if (date) {
                debugger;
                const params = this.getParams(date);
                this.$inertia.visit(`/planner${params}`, {
                    only: ["scheduled", "date"]
                });
            }
        },

        openItem(item = {}, type = "event") {
            this.isItemModalOpen = true;
            this.openedItem = item;
            this.boardType = type;
        },

        onItemSaved() {
            this.$nextTick(() => {
                this.isItemModalOpen = false
                this.$inertia.reload(`/planner${this.params}`, {
                    only: ["scheduled"],
                    preserveState: true
                });
            })
        }
    }
};
</script>

<style lang="scss">
.section-card {
    @apply bg-white overflow-hidden shadow-xl mx-2 mb-4 rounded-md;
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
