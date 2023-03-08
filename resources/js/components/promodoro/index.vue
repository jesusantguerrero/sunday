<template>
    <div class="promodoro-app" :class="{mini: isMiniLocal}">
        <header
            class="flex items-center justify-between w-full py-2 font-bold text-white "
            :class="promodoroColor"
        >
             <button
                    @click="play()"
                    class="ml-2"
                >
                <i class="material-icons">{{ icon }}</i>
                </button>
            <span> {{ title }} </span>
            <div class="flex">
                <!-- <div
                    class="flex h-8 rounded-lg actions"
                    :class="`bg-${promodoroColor}-700`"
                >
                    <button
                        v-for="(mode, key) in modes"
                        :key="key"
                        class="h-full px-2 rounded-lg"
                        :class="{
                            [`bg-${promodoroColor}-100 text-${promodoroColor}-700`]:
                                modeSelected == key
                        }"
                        @click="setMode(key)"
                    >
                        {{ mode.name }}
                    </button>
                </div> -->
                <button @click="findNextMode()"
                    class="ml-2"
                    title="Next session"
                >
                    <i class="fa fa-forward"></i>
                </button>
                <button
                    @click="toggleConfiguration"
                    class="ml-2"
                    title="Promodoro configuration"
                >
                    <i class="fa fa-cog"></i>
                </button>
                <button @click="toggleViewMode()"
                    class="ml-2"
                    title="Promodoro configuration"
                >
                    <i class="fa fa-chevron-down"></i>
                </button>
            </div>
        </header>
        <p v-if="isMiniLocal" class="mb-4 text-white uppercase">
            <span>{{ modes[modeSelected].name }}</span>
        </p>
        <div v-else class="clock" :class="{ rest: round, ticking: run == 1 }">
            <time class="time">{{ formattedTime }}</time
            ><span class="note">click here to {{ message }}</span
            ><span>{{ modes[modeSelected].name }}</span>
            <div class="inner-controls" @click="play">
                <i class="material-icons">{{ icon }}</i>
            </div>
        </div>
        <div class="promodoro__footer" v-if="!isMiniLocal">
            <div class="w-full">
                <multiselect
                    v-model="task"
                    label="title"
                    track-by="id"
                    placeholder="Select a task"
                    :show-labels="false"
                    :options="tasks"
                    class="w-full"
                >
                </multiselect>
            </div>
        </div>

        <promodoro-configuration-modal
            :settings="settings"
            :is-open="isConfigurationOpen"
            @cancel="toggleConfiguration"
            @saved="toggleConfiguration"
        >
        </promodoro-configuration-modal>
    </div>
</template>

<script>
const time = { minutes: 0, seconds: 10 };

import Tracker from "../timeTracker/tracker";
import { Duration } from "luxon";
import PromodoroConfigurationModal from "./Configuration.vue";
import promodoroMixin, { MODES } from "./promodoro";

export default {
    mixins: [promodoroMixin],
    props: {
        tasks: {
            type: Array,
            default() {
                return [];
            }
        },
        tracker: {
            type: Object
        },
        timerColor: {
            type: String
        },
        allowPause: {
            type: Boolean,
            default: false
        },
        isMini: {
            type: Boolean,
            default: true
        }
    },
    components: {
        PromodoroConfigurationModal
    },
    data() {
        return {
            time,
            startTime: null,
            startTimeStamp: null,
            expectedDuration: null,
            currentDuration: null,
            icon: "play_arrow",
            run: 0,
            timer: "",
            round: 0,
            isConfigurationOpen: false,
            promodoroTemplate: [],
            modeSelected: "session",
            task: [],
            trackerLocal: null,
            isMiniLocal: true
        };
    },
    mounted() {
        this.init();
    },

    beforeDestroy() {
        this.reset();
        this.$parent.$emit("session::stopped");
    },

    watch: {
        trackerLocal() {
            this.$emit("update:tracker", this.trackerLocal);
        },
        isMini() {
            this.isMiniLocal = this.isMini;
        },
        isMiniLocal() {
            this.$emit('update:is-mini', this.isMiniLocal)
        },
        promodoroColor: {
            handler() {
                this.$emit("update:timerColor", this.promodoroColor);

            },
            immediate: true
        },
        formattedTime: {
            deep: true,
            handler(formattedTime) {
                if (this.tracker) this.tracker["duration"] = this.tracker.getDuration();
                const title = this.run ? `(${formattedTime}) Daily` : "Daily";
                document.getElementsByTagName("title")[0].text = title;
                let [min, sec] = formattedTime.split(":")
                min = Number(min)
                sec = Number(sec)
                this.time.minutes = min >= 0 ? min : 0;
                this.time.seconds = sec >= 0 ? sec : 0;
            }
        }
    },

    computed: {
        message() {
            switch (this.run) {
                case 0:
                    return "start";
                case 1:
                    return this.allowPause ? "pause" : "stop";
                case 2:
                    return "resume";
            }
        },

        promodoroColor() {
            return this.modeSelected && MODES[this.modeSelected].color
                ? MODES[this.modeSelected].color
                : "bg-red-500";
        },

        rawTime() {
            let duration = 0;
            const expected = this.expectedDuration || Duration.fromMillis(0);
            if (this.startTime) {
                duration = Duration.fromMillis(this.currentDuration);
            }
            return duration ? expected.minus(duration) : expected;
        },

        title() {
           return this.isMiniLocal ? this.formattedTime : "Promodoro"
        },

        formattedTime() {
            return this.rawTime.toFormat("mm:ss")
        }
    },
    mounted() {
        this.init();
    },

    methods: {
        toggleViewMode() {
            this.isMiniLocal = !this.isMiniLocal;
        },
        play() {
            this.stopSound();
            switch (this.run) {
                case 0:
                    this.initTimer();
                    break;
                case 1:
                    this.stop();
                    break;
                case 2:
                    this.initTimer("resume");
                    break;
            }
        },

        reset() {
            this.stop();
            this.run = 0;
            this.round = 0;
            this.time = {
                minutes: this.modes.session.minutes,
                seconds: this.modes.session.seconds
            };
            this.updateExpectedDuration();
            this.modeSelected = "session";
        },

        stop(timestamp) {
            clearInterval(this.timer);
            this.stopTracker(timestamp);
            this.currentDuration = 0;
            this.run = this.allowPause ? 2 : 0;
            this.icon = "play_arrow";
            if (!this.allowPause) {
                this.setMode(this.modeSelected);
            }
        },

        stopTracker(stoppedTimestamp) {
            if (this.trackerLocal) {
                this.trackerLocal.stopTimer(stoppedTimestamp);
                this.tracker["duration"] = this.tracker.getDuration();
                this.$emit('stopped')
            }
        },

        endPromodoro(timestamp) {
            this.stop(timestamp)
            MessageBox.confirm(
                `The time of the ${this.modeSelected} has finished`
            ).then(() => {
                this.findNextMode();
                this.run = 0;
                if (this.modeSelected != 'session') {
                    this.play();
                }
            });
        },

        initTimer(selfMode) {
            this.run = 1;
            this.icon = this.allowPause ? "pause" : "stop";

            if (!selfMode) {
                this.time.minutes = this.modes[this.modeSelected].minutes;
            }

            this.updateExpectedDuration();

            if (this.modeSelected == "session" && this.task.id) {
                this.trackerLocal = new Tracker({
                    description: this.task.title,
                    item_id: this.task.id,
                    type: 'promodoro',
                    target_duration: this.expectedDuration
                });
                this.startTimeStamp = this.trackerLocal.startTimer();
                this.startTime = this.startTimeStamp.getTime();
                console.log(this.startTimeStamp);
            } else {
                this.startTime = Date.now();
            }

            this.timer = setInterval(() => {
                this.countDown();
            }, 100);
        },

        countDown() {
            this.currentDuration = Date.now() - this.startTime;
            this.checkTime();
        },

        updateExpectedDuration() {
            this.expectedDuration = Duration.fromISO(
                `PT${this.time.minutes}M${this.time.seconds}S`
            );
        },

        checkTime() {
            if (this.time.seconds == 0 && this.time.minutes == 0) {
                this.playSound();
                this.endPromodoro(new Date());
            }
        },

        setTask(task) {
            if (!this.task || this.task.id != task.id) {
                this.reset();
            }
            this.task = task;
            this.play();
        },

        setMode(modeName) {
            this.modeSelected = modeName;
            this.time.minutes = this.modes[modeName].minutes;
            this.time.seconds = this.modes[modeName].seconds;
            this.currentDuration = 0;
            this.updateExpectedDuration();
        },

        findNextMode() {
            const isLastMode = this.promodoroTemplate.length - 1 == this.round;
            this.round = isLastMode ? 0 : this.round + 1;
            const nextMode = this.promodoroTemplate[this.round];
            this.setMode(nextMode);
        },

        showNotification() {
            const permission = localStorage.getItem("permission");
            if (Notification && permission === "granted") {
                setTimeout(() => {
                    const notification = new Notification("Expired");
                }, 5000);
            } else if (permission !== "denied") {
                Notification.requestPermission().then(permission => {
                    localStorage.setItem("permission", permission);
                    new Notification("Expired", {
                        body: "Time has expired"
                    });
                });
            }
        },

        toggleConfiguration(settings) {
            if (this.isConfigurationOpen) {
                this.init(settings);
            }
            this.isConfigurationOpen = !this.isConfigurationOpen;
        }
    }
};
</script>

<style lang="scss" scoped>
.promodoro-app {
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    width: 100%;
    height: 100%;
    text-align: center;
}

.clock {
    @apply py-2;
    background: transparent;
    min-height: 200px;
    width: 100%;
    color: #fff;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    cursor: pointer;
    position: relative;
}

.clock:hover {
    box-shadow: 3px 4px 5px rgba(0, 0, 0, 0.2);
}

.clock:hover .inner-controls {
    opacity: 7;
}

.clock:before {
    content: "";
    position: absolute;
    width: 100%;
    height: 100%;
    background: rgba(255, 255, 255, 0.3);
    transition: all ease 1s;
    display: flex;
    flex-direction: row;
    justify-content: center;
    align-items: center;
    z-index: -1;
}

.clock.ticking:before {
    animation: ticking 3s infinite;
}

.title {
    margin: 0;
}

.time {
    font-size: 70px;
}

.inner-controls {
    position: absolute;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    opacity: 0;
    transition: all ease 1s;
    display: flex;
    flex-direction: row;
    justify-content: center;
    align-items: center;
}

.inner-controls .material-icons {
    font-size: 150px;
}

.outer-controls-container {
    display: flex;
    flex-direction: row;
    justify-content: space-around;
    align-items: center;
}

.outer-controls-container [class*="-control"] {
    margin: 5px;
    color: white;
}

@keyframes ticking {
    0% {
        transform: scale(1);
    }

    30% {
        transform: scale(1.1);
    }

    70% {
        transform: scale(1.1);
        border: 2px solid rgba(255, 255, 255, 0.3);
        background: transparent;
    }

    100% {
        transform: scale(1);
    }
}

.promodoro__footer {
    @apply bg-transparent text-white font-bold flex justify-between w-full items-center py-2 px-4;
}
</style>
