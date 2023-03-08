const timeEntry = {
    description: "",
    billable: 0,
    start: null,
    end: null,
    duration: null,
    label_ids: []
};

import { cloneDeep } from "lodash";
import { addMinutes, format as formatDate, isThisSecond } from "date-fns";
import axios from "axios";
import { Duration } from "luxon";
import { Interval } from "luxon";

export default class tracker {
        constructor(trackConfig = {}) {
            this.timeEntry = Object.assign(timeEntry, trackConfig)
            this.running= false
            this.now= null
            this.onUpdate = null
            this.interval = null
        }

        startTimer() {
            const formData = cloneDeep(this.timeEntry);
            const startDate = new Date();
            formData.start = formatDate(startDate, "yyyy-MM-dd HH:mm:ss");
            formData.label_ids = JSON.stringify(timeEntry.label_ids);

            axios
            .post("/api/time-entries", formData)
            .then(({ data }) => {
                this.running = true;
                this.timeEntry = data.data;
            })
            .catch(error => {
                this.$notify({
                title: "Error",
                message: error.response.data.status.message,
                type: "error"
                });
            });
            return startDate;
        }

        prepareForm() {
            const formData = cloneDeep(this.timeEntry);
            formData.start = formatDate(this.timeEntry.start, "yyyy-MM-dd HH:mm:ss");
            formData.label_ids = JSON.stringify(this.timeEntry.label_ids);
            return formData;
        }

        updateEntry(formData) {
            if (this.timeEntry.id) {
                formData = formData || this.prepareForm();
                return axios.put(`/api/time-entries/${this.timeEntry.id}`, formData);
            }
        }

        setCurrentTimer(data) {
            if (data && data.length) {
                const timeEntry = data[0];
                this.running = true;
                this.timeEntry = timeEntry;
            }
        }

        stopTimer(stoppedTimeStamp) {
            const start = new Date(this.timeEntry.start);
            if (stoppedTimeStamp && this.timeEntry.target_duration) {
                const duration = Duration.fromISO(this.timeEntry.target_duration);
                stoppedTimeStamp = addMinutes(start, duration.minutes);
                this.timeEntry.promodoro_completed = true;
            }
            this.timeEntry.end = formatDate(stoppedTimeStamp || new Date(), "yyyy-MM-dd HH:mm:ss");

            const formData = cloneDeep(this.timeEntry);
            formData.start = formatDate(start, "yyyy-MM-dd HH:mm:ss");
            formData.label_ids = JSON.stringify(this.timeEntry.label_ids);
            formData.status = 1;
            formData.duration = this.getDuration();
            formData.iso_duration = this.getDuration(false, 'ISO');
            this.updateEntry(formData).then(() => {
                this.running = false;
                clearInterval(this.interval)
                this.resetTimer();
            });
        }

        resetTimer() {
            this.timeEntry = cloneDeep(timeEntry);
        }

        toggleTimer() {
            this.running ? this.stopTimer() : this.startTimer();
        }

        trackTime() {
            this.interval = setInterval(() => {
                this.now = new Date();
                this.timeEntry.duration = this.getDuration()
            }, 100);
        }

        getDuration(formatted, formatGet) {
            let duration = 0;
            if (this.timeEntry.start) {
                const start = new Date(this.timeEntry.start);
                const end = this.timeEntry.end && new Date(this.timeEntry.end)
                duration = Interval.fromDateTimes(start, end || this.now || new Date()).toDuration();
            } else {
                duration = Duration.fromMillis(0);
            }

            if (!formatted & !formatGet) {
                return duration.as('milliseconds');
            } else if (formatGet == 'ISO') {
                return duration.toISO();
            } else {
                return duration.toFormat("hh:mm:ss");
            }
        }

        static durationFromMs(ms) {
            return Duration.fromMillis(ms).toFormat('hh:mm:ss');
        }
}
