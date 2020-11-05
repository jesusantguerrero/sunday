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
    data() {
        return {
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
            const modes = JSON.parse(localStorage.getItem('modes'))

            this.modes = {
                session: {
                    name: "session",
                    minutes: modes ? modes.session.minutes : SESSION_MINUTES,
                    seconds: modes ? modes.session.seconds : TIME_SECONDS
                },
                break: {
                    name: "break",
                    minutes: modes ? modes.break.minutes : BREAK_MINUTES,
                    seconds:  modes ? modes.break.seconds : TIME_SECONDS
                },

                longBreak: {
                    name: "long",
                    minutes: modes ? modes.longBreak.minutes : LONG_BREAK_MINUTES,
                    seconds: modes ? modes.longBreak.seconds : TIME_SECONDS
                }
            }
            this.reset && this.reset();
        },
    }
}
