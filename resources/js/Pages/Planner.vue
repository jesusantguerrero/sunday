<template>
    <app-layout :boards="boards">
        <div class="">
            <div
                class="max-w-8xl mx-auto sm:pr-6 lg:pr-8 flex flex-col md:flex-row"
            >
                <!-- Main board -->
                <div class="w-100 md:w-9/12 md:mx-4 pt-12">
                    <div
                        class="flex justify-between flex-col md:flex-row mx-2 md:mr-2 md:ml-6"
                    >
                        <div>
                            <span class="text-3xl font-bold">
                                Planner
                            </span>
                            <button class="btn bg-purple-400 text-white font-bold" @click="openItem()">
                                Add Event
                            </button>
                        </div>
                        <!-- <div class="controls h-12 bg-purple-700 rounded-lg">
                            <button
                                v-for="mode in modes"
                                :key="mode"
                                @click="modeSelected = mode"
                                :class="{
                                    'bg-purple-400': mode == modeSelected
                                }"
                                class="px-8 h-full rounded-lg text-white capitalize"
                            >
                                {{ mode }}
                            </button>
                        </div> -->
                    </div>

                    <div class="mt-5 md:ml-6">
                        <schedule-view
                            v-model="localDate"
                            :schedule="scheduled"
                            :link-fields="{
                                url_id: 'Join',
                                url_subject: 'See meet'
                            }"
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
                        <header
                            class="bg-blue-400 text-white font-bold flex justify-between"
                        >
                            <span>
                                Dailies
                            </span>
                            <button
                                class="bg-transparent text-white"
                                @click="openItem({}, 'daily')"
                            >
                                <i class="fa fa-plus"></i>
                            </button>
                        </header>
                        <div class="body text-gray-600 bg-blue-400"></div>
                    </div>
                    <div class="section-card committed mt-5">
                        <header
                            class="bg-blue-400 text-white font-bold flex justify-between"
                        >
                            <span>
                                Habits
                            </span>
                            <button
                                class="bg-transparent text-white"
                                @click="openItem({}, 'habit')"
                            >
                                <i class="fa fa-plus"></i>
                            </button>
                        </header>
                        <div class="body text-gray-600 bg-blue-400"></div>
                    </div>
                    <div class="section-card committed mt-5">
                        <header
                            class="bg-blue-400 text-white font-bold flex justify-between"
                        >
                            <span>
                                Notes
                            </span>
                            <button
                                class="bg-transparent text-white"
                                @click="isLinkFormOpen = !isLinkFormOpen"
                            >
                                <i class="fa fa-plus"></i>
                            </button>
                        </header>
                        <div class="body text-gray-600 bg-blue-400"></div>
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
import { subDays, toDate, format } from "date-fns";

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
        }
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
            localDate: null,
            isLoading: false,
            openedItem: {},
            isItemModalOpen: false
        };
    },
    created() {
        this.setCommitDate();
    },
    watch: {
        localDate: {
            handler(newDate, oldDate) {
                if (!oldDate || (newDate && format(newDate, 'yyyy-MM-dd') != format(oldDate, 'yyyy-MM-dd'))) {
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
        params() {
           return `?date=${format(this.localDate, 'yyyy-MM-dd')}`;
        }
    },
    methods: {
        setCommitDate() {
            let date = null;
            date = this.date.split("-");
            date = new Date(this.date);
            this.localDate = date;
        },

        getCommitsByDate() {
            this.$inertia.visit(`/planner${this.params}`, {
                only: ["scheduled", "date"],
                preserveState: true
            });
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
