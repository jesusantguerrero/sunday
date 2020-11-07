<template>
    <div class="card w-full time-traker-form bg-white  sm:px-6 lg:px-8 py-4">
      <div class="card-body flex">
        <div class="w-2/4">
          <input
            type="text"
            class="time-tracker__description"
            placeholder="Let's track that!"
            v-model.trim="timeEntry.description"
          />
        </div>
        <div class="w-1/4 flex">
          <div class="time-tracker__relations flex">
            <div class="time-tracker__billable-status custom-check-container">
              <label
                for="time-tracker-project"
                class="custom-check"
                :class="{ selected: timeEntry.project_id }"
                id="time-tracker-project-select"
                data-toggle="dropdown"
                aria-haspopup="true"
                aria-expanded="false"
                tabindex="0"
              >
                <i class="fa fa-folder"></i>
                <input
                  type="checkbox"
                  name="time-tracker-project"
                  class="hide"
                  id="time-tracker-project"
                  :checked="timeEntry.project_id"
                />
              </label>

              <div
                class="dropdown-menu time-tracker__dd"
                aria-labelledby="time-tracker-project-select"
              >
                <!-- <label-list-form
                  :selected="timeEntry.label_ids"
                  api-url="/labels"
                  label="title"
                  track-id="id"
                  placeholder="Find project..."
                  @select="timeEntry.label_ids.push($event)"
                  @remove="timeEntry.label_ids.splice($event, 1)"
                /> -->
              </div>
            </div>

            <div class="time-tracker__billable-status custom-check-container">
              <label
                for="time-tracker-billable"
                id="time-tracker-tag-select"
                class="custom-check  dropdown-toggle"
                :class="{ selected: timeEntry.label_ids.length }"
                data-toggle="dropdown"
                aria-haspopup="true"
                aria-expanded="false"
                tabindex="0"
                ref="tagSelect"
                @focus="focusTagSelect"
              >
                <i class="fa fa-tag"></i>
                <input
                  type="checkbox"
                  name="time-tracker-billable"
                  class="hide"
                  id="time-tracker-tags"
                  :checked="timeEntry.label_ids.length"
                />
              </label>

              <div
                class="dropdown-menu time-tracker__dd"
                aria-labelledby="time-tracker-tag-select"
              >
                <!-- <label-list-form
                  :selected="timeEntry.label_ids"
                  api-url="/labels"
                  label="title"
                  track-id="id"
                  :default-struct="{
                    title: '',
                    description: '',
                    color: '#f6f6f6',
                    color_format: 'hex'
                  }"
                  :search-attributes="['title', 'description']"
                  @select="timeEntry.label_ids.push($event)"
                  @remove="timeEntry.label_ids.splice($event, 1)"
                /> -->
              </div>
            </div>

            <div class="time-tracker__billable-status custom-check-container">
              <label
                for="time-tracker-billable"
                class="custom-check"
                :class="{ selected: timeEntry.billable }"
                tabindex="0"
              >
                <i class="fa fa-dollar-sign"></i>
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
        <div class="w-1/4 flex">
          <div class="time-tracker__controls flex">
            <input
              type="text"
              name=""
              id=""
              v-model="duration"
              disabled
              class="time-duration-display"
            />

            <button @click="toggleTimer()" class="play-button" :disabled="!timeEntry.description">
                <i  class="fa" :class="timerButtonIcon"></i>
            </button>
          </div>
        </div>
      </div>
    </div>
</template>

<script>
import { format as formatDate } from "date-fns";
import { cloneDeep } from "lodash-es";
import Duration from "duration";
import axios from "axios";

const timeEntryDefault = {
  description: "",
  billable: 0,
  start: null,
  end: null,
  duration: null,
  label_ids: []
};

export default {
    props: {
        current: {
            type: Array,
            default() {
                return []
            }
        }
    },
  data() {
    return {
      now: new Date(),
      running: false,
      timeEntry: {
        description: "",
        billable: 0,
        start: null,
        end: null,
        duration: null,
        label_ids: []
      }
    };
  },

  mounted() {
    this.trackTime();
    this.setCurrentTimer(this.current);
  },

  computed: {
    duration() {
      return this.timeEntry.duration || this.getDuration(true);
    },

    timerButtonIcon() {
      return this.running ? "fa-stop" : "fa-play";
    }
  },

  watch: {
    "timeEntry.description"() {
      if (this.timeEntry.start) {
        // this.updateEntry();
      }
    }
  },

  methods: {
    startTimer() {
      const formData = cloneDeep(this.timeEntry);
      formData.start = formatDate(new Date(), "yyyy-MM-dd HH:mm:ss");
      formData.label_ids = JSON.stringify(this.timeEntry.label_ids);

      axios
        .post("/time-entries", formData)
        .then(({ data }) => {
          this.running = true;
          this.timeEntry = data;
        })
        .catch(error => {
          this.$notify({
            title: "Error",
            message: error.response.data.status.message,
            type: "error"
          });
        });
    },

    prepareForm() {
      const formData = cloneDeep(this.timeEntry);
      formData.start = formatDate(this.timeEntry.start, "yyyy-MM-dd HH:mm:ss");
      formData.label_ids = JSON.stringify(this.timeEntry.label_ids);
      return formData;
    },

    updateEntry(formData) {
      formData = formData || this.prepareForm();
      return axios.put(`/time-entries/${this.timeEntry.id}`, formData);
    },

    setCurrentTimer(data) {
        if (data && data.length) {
            const timeEntry = data[0];
            this.running = true;
            this.timeEntry = timeEntry;
        }
    },

    stopTimer() {
      const formData = cloneDeep(this.timeEntry);
      formData.end = formatDate(new Date(), "yyyy-MM-dd HH:mm:ss");
      formData.start = formatDate(new Date(this.timeEntry.start), "yyyy-MM-dd HH:mm:ss");
      formData.label_ids = JSON.stringify(this.timeEntry.label_ids);
      formData.status = 1;
      formData.duration = this.getDuration();
      this.updateEntry(formData).then(() => {
        this.running = false;
        this.resetTimer();
        this.$emit("stopped");
      });
    },

    resetTimer() {
      this.timeEntry = cloneDeep(timeEntryDefault);
    },

    toggleTimer() {
      this.running ? this.stopTimer() : this.startTimer();
    },

    trackTime() {
      setInterval(() => {
        this.now = new Date();
      }, 1000);
    },

    getDuration(formatted) {
      let duration = 0;
      if (this.timeEntry.start) {
        const start = formatDate(new Date(this.timeEntry.start), "yyyy-MM-dd HH:mm:ss");
        duration = new Duration(new Date(start), this.now);
      } else {
          const date = new Date();
          duration = new Duration(date, date);
      }

      return formatted ? duration.toString("%H:%M:%S") : duration.milliseconds;
    },

    focusTagSelect(event) {
      if (event.relatedTarget) {
        this.$refs.tagSelect.click();
      }
    }
  }
};
</script>

<style lang="scss" scoped>
.time-tracker__controls {
  margin-left: auto;
  .play-button {
    margin: auto;
    background: dodgerblue;
    border: none;
    height: 45px;
    width: 45px;
    border-radius: 50%;
    color: white;
    outline: none;
    display: flex;
    align-items: center;
    justify-content: center;

    &:focus {
      outline: none;
    }

    &:hover {
      background: lighten($color: dodgerblue, $amount: 3);
    }
  }

  .time-duration-display {
    width: max-content;
    text-align: center;
    width: 90px;
    background: white;
    border: none;
    margin-right: 5px;
    font-weight: bolder;
    font-size: 20px;
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

  &.dropdown-toggle::after {
    content: "";
    border: none !important;
    width: 0 !important;
    height: 0 !important;
    user-select: none;
    margin: 0 !important;
  }
}

.time-tracker__description {
  border: none;
  height: 100%;
  width: 100%;
  font-size: 22px;

  &:focus {
    outline: none;
  }
}

.custom-check-container {
  width: 28px;
  height: 34px;
  display: flex;
  justify-content: center;
  align-items: center;
  text-align: center;
  margin: auto;
  position: relative;

  &:hover {
    background: rgba(0, 0, 0, 0.1);
    border-radius: 10px;
    color: #333;
  }
}

.dropdown-menu.time-tracker__dd.show {
  top: 10px !important;
  user-select: none;
  width: 250px;
  height: 300px;
  right: 50%;
  transform: translate3d(-43%, 36px, 0px) !important;
}
</style>
