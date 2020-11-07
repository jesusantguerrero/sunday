const SESSION_MINUTES= 25;
const BREAK_MINUTES= 5;
const LONG_BREAK_MINUTES= 15;
const TIME_SECONDS= 10;
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
    data() {
        return {
            audio: null,
            alarmSound: "alarmwatch",
            modes: {
                session: {
                    name: "session",
                    color: "red",
                    minutes: SESSION_MINUTES,
                    seconds: TIME_SECONDS
                },
                break: {
                    name: "break",
                    color: "blue",
                    minutes: BREAK_MINUTES,
                    seconds: TIME_SECONDS
                },
                longBreak: {
                    name: "long",
                    color: "green",
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
        init() {
            if (localStorage.getItem('promodoroTemplate')) {
                this.promodoroTemplate = JSON.parse(localStorage.getItem('promodoroTemplate'))
            }
            const modes = JSON.parse(localStorage.getItem('modes'));
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
            const audio = new Audio(`/audio/${this.alarmSound}.mp3`)
            audio.id = "audio"
            document.body.appendChild(audio);
            audio.currentTime = 0
            audio.play()
            this.audio = audio
            window.navigator.vibrate([1000, 100, 1000, 100, 1000, 100, 1000]);
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
