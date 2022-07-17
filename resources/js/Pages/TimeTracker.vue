<template>
    <app-layout :boards="boards">
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Dashboard
            </h2>
        </template>

        <div class="">
            <div class="flex mx-auto max-w-8xl">
                <time-entry-form :current="current" @stopped="reloadTracks">
                </time-entry-form>
            </div>

            <div class="mt-10 items-container">
                <div
                    class="flex justify-between px-8 mb-10 items-container__header"
                >
                    <span class="text-3xl font-bold"> Time entries </span>
                    <div class="bg-purple-700 rounded-lg controls">
                        <button
                            v-for="mode in modes"
                            :key="mode"
                            @click="modeSelected = mode"
                            :class="{ 'bg-purple-400': mode == modeSelected }"
                            class="h-full px-8 text-white capitalize rounded-lg"
                        >
                            {{ mode }}
                        </button>
                    </div>
                </div>
                <div class="px-8 items-container__list">
                    <div v-for="(tracksByDate, trackDate) in trackGroup" :key="trackDate" class="mb-12">
                        <div class="w-full py-4 pl-16 font-bold bg-white">
                            {{ formattedDate(trackDate) }}
                        </div>

                        <time-entry-group
                            v-for="track in tracksByDate"
                            :time-entry="track"
                            :key="track.id"
                        >
                        </time-entry-group>
                    </div>
                </div>
            </div>
        </div>

        <bulk-selection-bar
            v-if="selectedItems.length"
            :selected-items="selectedItems"
            @delete-pressed="confirmDeleteItems(selectedItems, true)"
        >
        </bulk-selection-bar>
    </app-layout>
</template>

<script>
import AppLayout from "./../Layouts/AppLayout.vue";
import TimeEntryForm from "../components/timeTracker/Form.vue";
import TimeEntryGroup from "../components/timeTracker/Group.vue";
import BulkSelectionBar from "../components/BulkSelectionBar.vue";
import { format, isToday, parse } from "date-fns";

export default {
    name: "Integrations",
    components: {
        AppLayout,
        TimeEntryForm,
        TimeEntryGroup,
        BulkSelectionBar
    },
    props: {
        current: {
            type: Array,
            default() {
                return [];
            }
        },
        tracks: {
            type: Array,
            default() {
                return [];
            }
        },
        boards: {
            type: Array,
            default() {
                return [];
            }
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
    created() {
        this.$parent.$on("session::stopped", () => {
            this.$nextTick(() => {
                this.reloadTracks();
            });
        });
    },
    data() {
        return {
            modes: ["list", "grid"],
            modeSelected: "list"
        };
    },
    computed: {
        trackGroup() {
            const trackGroup = {};

            this.tracks.forEach(track => {
                const date = format(new Date(track.start), "yyyy-MM-dd");

                if (!trackGroup[date]) {
                    trackGroup[date] = {
                        [track.description]: {
                            id: `group-${track.id}`,
                            description: track.description,
                            tracks: [track]
                        }
                    };
                } else {
                    if (!trackGroup[date][track.description]) {
                        trackGroup[date][track.description] = {
                                id: `group-${track.id}`,
                                description: track.description,
                                tracks: [track]
                        };
                    } else {
                        trackGroup[date][track.description].tracks.push(track);
                    }
                }
            });
            return trackGroup;
        },

        selectedItems() {
            return this.tracks.filter(item => item.selected).map(item => item.id);
        }
    },
    methods: {
        reloadTracks() {
            this.$inertia.reload({
                preserveScroll: true
            });
        },

        formattedDate(date) {
            const dateT = parse(date, 'yyyy-MM-dd', new Date());
            return isToday(dateT) ? 'Today' : format(dateT, 'E, dd LLL yyyy')
        },

        confirmDeleteItems(items, reload = true) {
            this.showConfirm({
                title: `Deleting ${items.length} tasks`,
                content: "Are you sure you want to delete these tasks?",
                confirmationButtonText: "Yes, delete",
                confirm: () => {
                    axios({
                        url: `/api/time-entries/bulk/delete`,
                        method: "post",
                        data: items
                    }).then(() => {
                        this.$inertia.reload({ preserveScroll: true });
                    });
                }
            });
        },
    }
};
</script>

<style lang="scss">
button {
    &:focus {
        outline: 0 !important;
    }
}
</style>
