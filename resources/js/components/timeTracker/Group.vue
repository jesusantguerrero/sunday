<template>
    <div>
        <div
            v-if="timeEntry.tracks.length > 1"
            class="flex items-center w-full px-8 bg-white time-tracker-item"
        >
            <div class="flex w-full">
                <div class="flex items-center w-2/5">
                    <div class="mr-9">
                        <input type="checkbox" v-model="selected" @change="toggleSelection" />
                    </div>

                    <div class="flex">
                        <div
                            class="time-tracker-item__count"
                            @click.stop="toggleExpand()"
                        >
                            {{ timeEntry.tracks.length }}
                        </div>

                        <span
                            type="text"
                            class="mr-2 time-tracker__description"
                        >
                            {{ timeEntry.description }}
                        </span>


                    </div>
                </div>

                <div class="flex w-3/5 ml-auto">
                    <div class="flex time-tracker__controls">
                        <span disabled class="flex items-center start-dates">
                            {{ timeEntry.tracks[0].start | formatDateToTime }} -
                            {{
                                timeEntry.tracks[0].end | formatDateToTime
                            }}</span
                        >
                        <input
                            type="text"
                            name=""
                            :value="duration"
                            disabled
                            class="time-duration-display"
                        />

                        <button @click="initTimer()" class="play-button">
                            <i class="fa fa-play" />
                        </button>

                        <button @click="toggleExpand" class="play-button">
                            <i class="fa fa-th-list" />
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Child tracks -->
        <transition-group>
            <template v-if="timeEntry.tracks.length <= 1 || isExpanded">
                <time-entry-item
                    v-for="track in timeEntry.tracks"
                    :time-entry="track"
                    :key="track.id"
                >
                </time-entry-item>
            </template>
        </transition-group>
        <!-- end of child tracks -->
    </div>
</template>

<script>
import { format as formatDate } from "date-fns";
import TimeEntryItem from "./Item.vue";

export default {
    props: {
        timeEntry: {
            type: Object,
            default() {
                return {
                    description: "",
                    billable: false,
                    start: null,
                    end: null,
                    duration: null
                };
            }
        }
    },
    components: {
        TimeEntryItem
    },
    data() {
        return {
            now: new Date(),
            isExpanded: false,
            selected: false
        };
    },

    filters: {
        formatDateToTime(date) {
            return formatDate(new Date(date), "HH:mm:ss");
        }
    },

    computed: {
        duration() {
            const milliseconds = this.timeEntry.tracks.reduce(
                (total, current) => {
                    return (total += current.duration);
                },
                0
            );
            return this.durationFromMs(milliseconds);
        }
    },

    methods: {
        durationFromMs(ms) {
            const date = new Date(ms);
            return date
                .toISOString()
                .slice(11, -2)
                .split(":")
                .map(unit => {
                    return Math.round(unit)
                        .toString()
                        .padStart(2, "0");
                })
                .join(":");
        },

        toggleExpand() {
            this.isExpanded = !this.isExpanded;
        },

        toggleSelection() {
            this.selected
            this.timeEntry.tracks.forEach(
                track =>
                track['selected'] = this.selected
            );
        }
    }
};
</script>

<style lang="scss" scoped>
.time-tracker-item {
    height: 64px;
    margin: 2px 0;
    overflow: hidden;

    .card-body {
        padding: 0 !important;
    }

    &__count {
        border: 2px solid var(--primary-color);
        width: 30px;
        height: 30px;
        min-width: 30px;
        border-radius: 50%;
        display: flex;
        justify-content: center;
        align-items: center;
        margin-right: 10px;
        cursor: pointer;
        font-weight: bolder;
        color: var(--primary-color);
    }
}

.time-tracker__controls {
    margin-left: auto;
    .play-button {
        margin: auto;
        border: none;
        height: 45px;
        width: 45px;
        border-radius: 50%;
        color: #eee;
        background: transparent;
        outline: none;
        display: flex;
        align-items: center;
        justify-content: center;

        &:focus {
            outline: none;
        }

        &:hover {
            color: #333;
        }
    }

    .time-duration-display {
        width: max-content;
        text-align: center;
        width: 90px;
        background: white;
        border: none;
        display: flex;
        margin-right: 5px;
    }
}

.time-tracker__relations {
    margin-left: auto;
}

.custom-check {
    cursor: pointer;
    width: 100%;
    height: 25px;
    display: flex;
    justify-content: center;
    align-items: center;
    text-align: center;
    color: gray;
    transition: all ease 0.3s;
    margin-bottom: 0;

    input {
        display: none;
    }

    &.selected {
        color: var(--primary-color) !important;
    }
}

.custom-check-container {
    min-width: 28px;
    height: 34px;
    display: flex;
    justify-content: center;
    align-items: center;
    text-align: center;
    margin: auto;
    padding: 0 5px;

    &:hover {
        background: rgba(0, 0, 0, 0.1);
        border-radius: 10px;
        color: #333;
    }
}

.time-tracker__description {
    border: none;
    height: 100%;
    width: 100%;

    &:focus {
        outline: none;
    }
}

.start-dates {
    margin-right: 28px;
}
[class*="col"] {
    height: 100%;
}
</style>
