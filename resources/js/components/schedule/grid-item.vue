<template>
    <div
        class="day-slot"
        :class="{
            'on-air-program': isCurrent,
            'past-hour': hasPassed
        }"
    >
        <div class="content-body">
            <div class="image_cover">
                <span>
                    {{ daySlot.title[0] }}
                </span>
            </div>
            <div class="program-name">
                <div class="pl-1s">
                    <h4 class="day-slot__title">
                        <div class="">
                            {{ daySlot.title }}
                        </div>

                        <!-- CRUD Controls  -->
                        <div class="crud-controls">
                            <span
                                class="mx-2 px-2 cursor-pointer hover:text-blue-500"
                                v-if="daySlot.id && allowUpdate"
                                @click="$emit('update-called', daySlot)"
                            >
                                <i class="fa fa-edit"></i>
                            </span>

                            <span
                                class="mx-2 px-2 cursor-pointer rounded transition-all hover:text-red-700"
                                v-if="daySlot.id && allowDelete"
                                @click="$emit('delete-called', daySlot)"
                            >
                                <i class="fa fa-trash"></i>
                            </span>
                        </div>
                        <!-- End of CRUD controls -->
                    </h4>
                </div>
                <div class="day-slot__description pl-1">
                    {{
                        daySlot.descripcion && daySlot.descripcion.slice(0, 150)
                    }}
                    {{
                        daySlot.descripcion && daySlot.descripcion.length > 150
                            ? "..."
                            : ""
                    }}
                </div>
            </div>
        </div>

        <div class="hours-container">
            <div class="hours-container__left">
                <div class="hours-container__divider"></div>
                <div class="hours-container__item">
                    <span class="block" v-if="isCurrent"> From</span>
                    <span class="block">
                        {{ formatHour(daySlot[this.timeField]) }}</span
                    >
                    <span class="block" v-if="isCurrent"> to </span>
                    <span class="block mx-2" v-else> - </span>
                    <span class="block">
                        {{ formatHour(daySlot[this.timeEndField]) }}
                    </span>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { format, isAfter, isBefore, isEqual, isToday } from "date-fns";

export default {
    props: {
        daySlot: {
            type: Object,
            required: true
        },
        allowUpdate: {
            type: Boolean,
            default: false
        },
        allowDelete: {
            type: Boolean,
            default: false
        },
        currentTime: {
            type: Date,
            required: true
        },
        showAll: {
            type: Boolean
        },
        dateField: {
            type: String,
            default: "date"
        },
        timeField: {
            type: String,
            default: "time"
        },
        timeEndField: {
            type: String,
            default: "end_time"
        },
        dateEndField: {
            type: String,
            default: "due_date"
        }
    },
    computed: {
        isCurrent() {
            const hasTimeFields = this.daySlot[this.timeField] && this.daySlot[this.timeEndField];

            if (hasTimeFields && this.currentTime) {
                const startTime = this.setTime( this.daySlot[this.timeField], this.setDate(this.daySlot[this.dateField]));

                const endTime = this.setTime(this.daySlot[this.timeEndField], this.setDate(this.daySlot[this.dateEndField] || this.daySlot[this.dateField]));
                const isGreaterThanCurrent = isAfter(this.currentTime, startTime) || isEqual(this.currenTime, startTime);

                const isMenorThanFinal = isBefore(this.currentTime, endTime) ||  isEqual(this.currentTime, endTime);
                return isGreaterThanCurrent && isMenorThanFinal;
            }
            return false;
        },

        hasPassed() {
            const isStringDate = typeof this.daySlot[this.timeField] == "string";
            if (isStringDate && this.daySlot[this.timeEndField]) {
                const endDate = this.setTime(this.daySlot[this.timeEndField], this.setDate(this.daySlot[this.dateEndField] || this.daySlot[this.dateField]));
                const isGreaterThanFinal = isAfter(this.currentTime, endDate);
                return (isGreaterThanFinal && isToday(this.currentTime) && !this.showAll);
            } else {
                return (Number(this.daySlot[this.timeField]) < this.daySlot[this.timeField] && isToday(this.currentTime) && !this.showAll);
            }
        }
    },
    methods: {
        formatHour(hour) {
            if (hour) {
                const date = new Date();
                const time = hour.split(":");
                date.setHours(time[0]);
                date.setMinutes(time[1]);
                return format(date, "hh:mm a");
            }
        },
        setDate(dateValue) {
            const date = dateValue ? dateValue.split("-") : null;
            return date ? new Date(date[0], date[1] - 1, date[2]) : null;
        },

        setTime(data, dateValue) {
            const date = dateValue || new Date();
            const hourDate = data.split(":");
            date.setHours(hourDate[0]);
            date.setMinutes(hourDate[1]);
            date.setSeconds(0);
            return date;
        }
    }
};
</script>

<style lang="scss">
.day-slot {
    @apply flex justify-between border-gray-200 border-2 shadow-sm;
    position: relative;
    border-radius: var(--primary-radius);
    background: white;
    box-shadow: 4px 8px 8px rgba(255, 255, 255, 0.5);
    height: 80px;
    margin-bottom: 8px;
    padding: 15px 12px 16px 12px;

    &::before {
        @apply bg-blue-400 rounded-lg;
        content: "";
        left: -3px;
        position: absolute;
        top: 25px;
        width: 5px;
        height: 20px;
    }
}

.badge-on-air {
    font-size: 17px;
    height: 25px;
    width: 84px;
    color: #231f20 !important;
    font-family: "Poppins", sans-serif;
    box-shadow: 4px 4px 4px rgba(255, 255, 255, 0.5);
}

.on-air-program {
    @apply border-purple-400;
    height: 152px !important;
    min-height: 152px !important;
    color: #707070;

    .program-name {
        margin-left: 25px;
    }

    .day-slot__title,
    .day-slot__description {
        margin-left: 5px;
    }

    .hours-container {
        justify-content: flex-start;

        &__divider {
            height: 67.796px;
            width: 1px;
        }

        &__item {
            margin-left: 35px;
            display: flex;
            flex-flow: column;
        }
    }
}

.content-body {
    @apply flex;
    color: var(--primary-color);
    align-items: center;
}

.day-slot__title {
    @apply flex items-center justify-between font-medium;
    margin-left: 10px;
    margin-bottom: 2px;
    font-size: 20px;
    color: #414042;
}

.program-name {
    // margin-top: 8px;
}

.day-slot__description {
    @apply font-light;
    margin-left: 10px;
    font-size: 16px;
}

.crud-controls {
    @apply flex justify-end items-center;
}

.hours-container {
    @apply flex items-center justify-between font-light;
    font-size: 15px;
    width: 250px;
    min-width: 250px;
    overflow: hidden;
    flex-direction: row !important;
    color: #414042;

    &__divider {
        height: 67.796px;
        width: 1px;
        background: #aaa;
    }

    &__left {
        display: flex !important;
        align-items: center;
    }

    &__item {
        display: flex;
        margin-left: 57px;
    }
}

.live-dot {
    animation: 1s pulse infinite;
    transition: all ease 0.3s;
    height: 8px;
    width: 8px;
}

@keyframes pulse {
    0% {
        opacity: 0.2;
        box-shadow: 4px 4px 4px red;
    }
    100% {
        height: 8px !important;
        width: 8px !important;
        opacity: 1;
    }
}

@media (max-width: 768px) {
    .day-slot {
        @apply flex flex-col justify-center items-center border-gray-200 border-2 py-2 px-2 my-2 shadow-sm;
        position: relative;
        border-radius: var(--primary-radius);
        height: fit-content !important;

        &.on-air-program {
            @apply py-4;

            .hours-container {
                @apply py-4;
                width: 100%;
                &__divider {
                    display: none;
                }
                &__left {
                    width: 100%;
                }

                span {
                    margin: 0 5px;
                }
                &__item {
                    @apply border-l-0 flex flex-row w-full;
                    justify-content: center;
                    width: 100%;
                    padding: 0 0 0 0;
                    margin: 0 0 0 0;
                }
            }
        }
    }

    .day-slot__title {
        @apply flex-col;
    }

    .content-body {
        @apply flex-col px-1 items-center;

        .program-name {
            @apply ml-0;
        }
    }

    .hours-container {
        @apply border-l-0 py-4;

        &__divider {
            display: none;
        }
    }

    .program-name {
        text-align: center;
    }

    .badge-on-air {
        margin: 15px auto;
    }
    .crud-controls {
        display: none;
    }

    .content-body {
        width: 100%;
    }

    .image_cover {
        margin-bottom: 5px;
    }

    .day-slot__description,
    .day-slot__title {
        margin: auto;
        padding: 0 0 0 0 !important;
    }
}
</style>
