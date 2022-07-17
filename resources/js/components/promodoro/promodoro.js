const SESSION_MINUTES= 25;
const BREAK_MINUTES= 5;
const LONG_BREAK_MINUTES= 15;
const TIME_SECONDS= 0;
const PROMODORO_TEMPLATE = [
    'session',
    'break',
    'session',
    'break',
    'session',
    'break',
    'session',
    'longBreak'
]

export default {
    props: {
        settings: {
            type: Object,
            default() {
                return {

                }
            }
        }
    },
    data() {
        return {
            audio: null,
            alarmSound: "alarmwatch",
            modes: {
                session: {
                    name: "session",
                    color: "bg-red-500",
                    minutes: SESSION_MINUTES,
                    seconds: TIME_SECONDS
                },
                break: {
                    name: "break",
                    color: "bg-blue-500",
                    minutes: BREAK_MINUTES,
                    seconds: TIME_SECONDS
                },
                longBreak: {
                    name: "long",
                    color: "bg-green-500",
                    minutes: LONG_BREAK_MINUTES,
                    seconds: TIME_SECONDS
                }
            },
            defaults: {
                SESSION_MINUTES,
                BREAK_MINUTES,
                LONG_BREAK_MINUTES,
                TIME_SECONDS,
                PROMODORO_TEMPLATE
            }
        }
    },
    methods: {
        init(settings) {
            const localSettings = settings || this.settings;
            if (localSettings.promodoro_template) {
                this.promodoroTemplate = JSON.parse(localSettings.promodoro_template)
            }
            const modes = localSettings.promodoro_modes.trim() && JSON.parse(localSettings.promodoro_modes);
            const { session, break: rest, longBreak} = this.modes;
            if (modes) {
                session.minutes = modes.session.minutes;
                session.seconds = modes.session.seconds;
                rest.minutes = modes.break.minutes;
                rest.seconds =   modes.break.seconds;
                longBreak.minutes = modes.longBreak.minutes;
                longBreak.seconds = modes.longBreak.seconds
            }
            this.reset && this.reset();
        },

        playSound() {
            // const audio = new Audio(`/audio/${this.alarmSound}.mp3`)
            // audio.id = "audio"
            // document.body.appendChild(audio);
            // audio.currentTime = 0
            // audio.play()
            // this.audio = audio
            // window.navigator.vibrate([1000, 100, 1000, 100, 1000, 100, 1000]);
        },

        stopSound() {
            if (this.audio && this.audio.pause) {
                this.audio.pause()
                this.audio.remove()
                window.navigator.vibrate(0);
            }
        },
    }
}
