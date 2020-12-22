<template>
  <div class="">
    <el-popover v-model="show" placement="bottom" width="250">
      <div v-if="!customDate" class="dropdown">
        <ul class="list-group list-group-flush dropdown__body">
          <button
            v-for="(shortcut, i) in shortcuts[mode]"
            :key="`single-shortcut-${i}`"
            class="list-group-item dropdown-item"
            @click="getDateValue(shortcut.start, shortcut.end, true)"
          >
            {{ shortcut.text }}
          </button>
          <button
            class="list-group-item dropdown-item"
            @click="customDate = true"
          >
            Custom Date
          </button>
        </ul>
      </div>

      <div v-else>
        <div>
          <button class="btn btn-block btn-primary" @click="customDate = false">
            back
          </button>
        </div>
        <div class="pt-2 form-group">
          <label for="">From </label>
          <el-date-picker
            v-model="dates.start"
            type="date"
            placeholder="Pick a day"
          />
        </div>

        <div class="form-group">
          <label for=""> To </label>
          <el-date-picker
            v-if="rangeMode"
            v-model="dates.end"
            type="date"
            placeholder="Pick a day"
          />
        </div>
      </div>
      <button slot="reference" class="btn btn-block range-selector">
        {{ currentValue || "Date Range" }}
      </button>
    </el-popover>
  </div>
</template>

<script>
import { format as formatDate } from "date-fns";
const DAY = 3600 * 1000 * 24;
export default {
  props: {
    rangeMode: {
      type: Boolean,
      default: false
    },
    value: {
      type: Object,
      required: true
    }
  },
  data() {
    return {
      show: false,
      dates: {
        start: "",
        end: ""
      },
      customDate: false
    };
  },
  computed: {
    mode() {
      return this.rangeMode ? "range" : "single";
    },
    shortcuts() {
      return {
        single: [
          {
            text: "Today",
            start: 0
          },
          {
            text: "Yesterday",
            start: -3600 * 1000 * 24
          },
          {
            text: "A week ago",
            start: -3600 * 1000 * 24 * 7
          }
        ],
        range: [
          {
            text: "Last week",
            start: -DAY * 7,
            end: 0
          },
          {
            text: "Last Month",
            start: -DAY * 30,
            end: 0
          },
          {
            text: "Last 3 months",
            start: -DAY * 90,
            end: 0
          }
        ]
      };
    },

    currentValue() {
      let date = "";
      if (this.rangeMode && this.dates.end) {
        date = `From ${formatDate(this.dates.start, "YYYY-MM-DD")} to 
            ${formatDate(this.dates.end, "YYYY-MM-DD")}`;
      } else if (this.dates.start) {
        date = `${formatDate(this.dates.end, "YYYY-MM-DD")}`;
      }
      return date;
    }
  },
  watch: {
    value: {
      handler(date) {
        this.dates.start = date.start;
        this.dates.end = date.end;
      },
      immediate: true
    }
  },
  methods: {
    getDateValue(startValue, endValue, close) {
      const startDate = new Date();
      const endDate = endValue >= 0 ? new Date() : endValue;

      startDate.setTime(startDate.getTime() + startValue);
      this.setDate(startDate);

      if (endDate) {
        endDate.setTime(endDate.getTime() + endValue);
      }
      this.setDate(endDate, "end", close);
    },

    setDate(date, prop = "start", close) {
      this.$set(this.dates, prop, date);
      this.$emit("input", this.dates);
      if (close) {
        this.show = false;
      }
    }
  }
};
</script>

<style lang="scss">
.dropdown-item:hover {
  background: var(--primary-color);
  color: white;
}

.range-selector {
  overflow: hidden;
  width: 100%;
  border-radius: 0 0 0 0;
  &:active, &:focus {
      box-shadow: none;
  }
}
</style>
