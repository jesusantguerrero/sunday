<template>
    <div class="promodoro-app">
        <header
            class=" text-white font-bold flex justify-between w-full items-center py-2"
            :class="`bg-${promodoroColor}-400`"
        >
            <span> Promodoro </span>
            <div class="flex">
                <div class="actions rounded-lg flex h-8"
                :class="`bg-${promodoroColor}-700`"
                >
                    <button
                        v-for="(mode, key) in modes"
                        :key="key"
                        class="px-2 h-full rounded-lg"
                        :class="{ [`bg-${promodoroColor}-100 text-${promodoroColor}-700`]: modeSelected == key }"
                        @click="setMode(key)"
                    >
                        {{ mode.name }}
                    </button>
                </div>
                <button @click="toggleConfiguration" class="ml-2" title="Promodoro configuration">
                    <i class="fa fa-cog"></i>
                </button>
            </div>
        </header>
        <div class="clock" :class="{ rest: round, ticking: run == 1 }">
            <time class="time">{{ formattedTime }}</time
            ><span class="note">click here to {{ message }}</span
            ><span>{{ modes[modeSelected].name }}</span>
            <div class="inner-controls" @click="play">
                <i class="material-icons">{{ icon }}</i>
            </div>
        </div>
        <div class="promodoro__footer">
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
            :is-open="isConfigurationOpen"
            @cancel="toggleConfiguration"
            @saved="toggleConfiguration">
        </promodoro-configuration-modal>
    </div>
</template>

<script>
const time = { minutes: 0, seconds: 10 };

import Tracker from "../timeTracker/tracker";
import PromodoroConfigurationModal from "./Configuration"
import promodoroMixin from "./promodoro"

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
        }
    },
    components: {
        PromodoroConfigurationModal
    },
    data() {
        return {
            time,
            icon: "play_arrow",
            run: 0,
            timer: "",
            round: 0,
            isConfigurationOpen: false,
            promodoroTemplate: [],
            modeSelected: "session",
            task: [],
            track: null
        };
    },
    mounted() {
        this.init()
    },

    beforeDestroy() {
        this.reset();
        this.$parent.$emit("session::stopped");
    },

    watch: {
        track() {
            this.$emit('update:tracker', this.track)
        },
        promodoroColor() {
            this.$emit('update:timerColor', this.promodoroColor)
        },
        time: {
            deep: true,
            handler() {
                const title = this.run ? `(${this.formattedTime}) Daily` : 'Daily'
                document.getElementsByTagName('title')[0].text = title;
            }
        }
    },


    computed: {
        message() {
            switch (this.run) {
                case 0:
                    return "start";
                case 1:
                    return "pause";
                case 2:
                    return "resume";
            }
        },

        promodoroColor() {
            return this.modeSelected && this.modes[this.modeSelected].color ? this.modes[this.modeSelected].color : 'red';
        },

        formattedTime() {
            let { minutes, seconds } = this.time;
            seconds = seconds < 10 ? (seconds = "0" + seconds) : seconds;
            minutes = minutes < 10 ? (minutes = "0" + minutes) : minutes;
            return `${minutes}:${seconds}`;
        }
    },
    mounted() {
        this.init();
    },

    methods: {
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

        stop() {
            this.stopTracker();
            clearInterval(this.timer);
            this.run = 2;
            this.icon = "play_arrow";
        },

        stopTracker() {
            if (this.track) {
                this.track.stopTimer();
                this.$set(this.tracker, 'duration', this.tracker.getDuration());
                this.$inertia.on('success', (event) => {
                    this.$nextTick(() => {
                        this.track = null;
                    })
                })
                this.$inertia.reload({
                    only: ['todo'],
                    preserveState: true
                });
            }
        },

        showNotification() {
            const permission = localStorage.getItem('permission')
            if (Notification && permission === 'granted') {
                setTimeout(() => {
                    const notification = new Notification("Expired");
                }, 5000)
            } else if (permission !== 'denied') {
                Notification.requestPermission().then(permission => {
                    localStorage.setItem('permission', permission)
                    new Notification("Expired", {
                        body: "Time has expired"
                    });
                })
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
            this.modeSelected = "session";
        },

        clear() {
            this.stop();
            if (confirm(`the time of the ${this.modeSelected} has finished`)) {
                const isLastMode = this.promodoroTemplate.length - 1 == this.round;
                this.round = isLastMode ? 0 : this.round + 1;
                const nextMode = this.promodoroTemplate[this.round];
                this.setMode(nextMode);
                this.run = 0;
            }
        },

        initTimer(selfMode) {
            this.run = 1;
            this.icon = "pause";

            if (!selfMode) {
                this.time.minutes = this.modes[this.modeSelected].minutes;
            }

            if (this.modeSelected == "session" && this.task.id) {
                this.track = new Tracker({
                    description: this.task.title,
                    item_id: this.task.id
                });
                this.track.startTimer();
            }

            this.timer = setInterval(() => {
                if (this.tracker) {
                    this.$set(this.tracker, 'duration', this.tracker.getDuration());
                }
                this.countDown();
            }, 1000);
        },

        setTask(task) {
            if (!this.task || this.task.id != task.id) {
                this.reset();
            }
            this.task = task;
            this.play();
        },

        countDown() {
            if (this.time.seconds == 0) {
                if (this.time.minutes > 0) {
                    this.time.minutes--;
                    this.time.seconds = 59;
                } else {
                    this.playSound()
                    setTimeout(() => {
                        this.clear();
                    }, 200)
                }
            } else {
                this.time.seconds--;
            }
        },

        addTime(property) {
            const self = this;
            this.modes[property].minutes = Number(this.modes[property].minutes ) + 1;
            const { minutes, seconds} = this.modes[property]

            if (property == "session" && this.round == 0) {
                self.updateTime(minutes, seconds);
            } else if (property == "break" && this.round == 1) {
                self.updateTime(minutes, seconds);
            }

            this.stop();
        },

        removeTime(property) {
            const self = this;
            if (this.modes[property].minutes > 0) {
                this.modes[property].minutes-=1;
            }
            const { minutes, seconds} = this.modes[property]

            if (property == "session" && this.round == 0) {
                self.updateTime(minutes, seconds);
            } else if (property == "break" && this.round == 1) {
                self.updateTime(minutes, seconds);
            }

            this.stop();
        },

        updateTime(mins, secs = 0) {
            this.time.minutes = mins;
            this.time.seconds = secs;
        },

        setMode(modeName) {
            this.modeSelected = modeName;
            this.time.minutes = this.modes[modeName].minutes;
            this.time.seconds = this.modes[modeName].seconds;
        },

        toggleConfiguration() {
            if (this.isConfigurationOpen) {
                this.init();
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
    @apply py-5;
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
    font-size: 90px;
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

.cs-row {
    width: 100%;
    height: 48px;
    display: flex;
    flex-direction: row;
    justify-content: center;
    align-items: center;
}

.cs-row button {
    border: 0;
    height: 100%;
    color: white;
    padding: 5px;
    background: transparent;
    display: flex;
    flex-direction: row;
    justify-content: center;
    align-items: center;
    display: inline-flex;
    cursor: pointer;
}

.cs-row button.reset-btn {
    margin: 5px 0;
}

.cs-row button:hover {
    background: transparent;
}

.cs-row .value {
    display: inline-block;
    height: 100%;
    padding: 0 10px;
    display: flex;
    flex-direction: row;
    justify-content: center;
    align-items: center;
    background: transparent;
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
