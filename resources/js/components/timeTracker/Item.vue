<template>
  <div class="w-full time-tracker-item flex items-center bg-white px-8">
    <div class="flex w-full">
      <div class="w-2/5 flex items-center">
        <input
          type="text"
          class="time-tracker__description"
          placeholder="Add description"
          v-model="timeEntry.description"
        />
        <div class="time-tracker__billable-status custom-check-container">
          <label
            for="time-tracker-billable"
            class="custom-check"
            :class="{ selected: timeEntry.billable }"
          >
            <i class="fa fa-folder" />
            <input
              type="checkbox"
              name="time-tracker-billable"
              class="hide"
              :id="`time-tracker-billable-${timeEntry.id}`"
              v-model="timeEntry.billable"
            />
          </label>
        </div>
      </div>

      <div class="w--1/5 flex m-auto">
        <div class="time-tracker__relations flex">
          <div class="time-tracker__billable-status custom-check-container">
            <label
              for="time-tracker-billable"
              class="custom-check"
              :class="{ selected: timeEntry.billable }"
            >
              <i class="fa fa-tag" /> {{ labels }}
              <input
                type="checkbox"
                name="time-tracker-billable"
                class="hide"
                id="time-tracker-billable"
                v-model="timeEntry.billable"
              />
            </label>
          </div>

          <div class="time-tracker__billable-status custom-check-container">
            <label
              for="time-tracker-billable"
              class="custom-check"
              :class="{ selected: timeEntry.billable }"
            >
              <i class="fa fa-dollar-sign" />
              <input
                type="checkbox"
                name="time-tracker-billable"
                class="hide"
                id="time-tracker-billable"
                v-model="timeEntry.billable"
              />
            </label>
          </div>
        </div>
      </div>
      <div class="w-2/5 flex ml-auto">
        <div class="time-tracker__controls flex">
          <span disabled class="flex items-center start-dates">
            {{ timeEntry.start | formatDateToTime }} -
            {{ timeEntry.end | formatDateToTime }}</span
          >

          <input
            type="text"
            name=""
            v-model="duration"
            disabled
            class="time-duration-display"
          />

          <button @click="initTimer()" class="play-button">
            <i :class="`fa fa-${timerButtonIcon}`" />
          </button>
          <button @click="deleteItem()" class="play-button">
            <i class="fa fa-trash" />
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { format as formatDate } from "date-fns";
import Duration from "duration";

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
  data() {
    return {
      now: new Date(),
      running: false
    };
  },

  filters: {
    formatDateToTime(date) {
      return formatDate(new Date(date), "HH:mm:ss");
    }
  },

  computed: {
    duration() {
        console.log(this)
      return this.durationFromMs(this.timeEntry.duration) || this.localDuration;
    },

    localDuration() {
      let duration = 0;
      if (this.timeEntry.start) {
        duration = new Duration(new Date(this.timeEntry.start), new Date(this.timeEntry.end));
        return duration.toString("%H:%M:%S");
      }

      return "00:00:00";
    },

    timerButtonIcon() {
      return this.running ? "stop" : "play";
    },

    labels() {
      return this.timeEntry.labels ? this.timeEntry.labels.map(label => label.title).join(", ") : "";
    }
  },

  methods: {
    deleteItem() {
        this.showConfirm({
            title: `Delete ${this.timeEntry.description}`,
            content: "Are you sure you want to delete this entry?",
            confirmationButtonText: "Yes, delete",
            confirm: () => {
                axios.delete(`time-entries/${this.timeEntry.id}`).then(() => {
                    this.$inertia.reload({
                        only: ['tracks'],
                        preserveState: true
                    })
                })
            }
        })
    },


    durationFromMs(ms) {
        const date = new Date(ms);
        return date.toISOString().slice(11, -2).split(':').map((unit) => {
            return Math.round(unit).toString().padStart(2,'0')
        }).join(":")
    },

    toggleTimer() {
      this.startTimer();
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
