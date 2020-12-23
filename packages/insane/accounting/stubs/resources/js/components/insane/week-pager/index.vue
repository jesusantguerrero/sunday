<template>
  <div>
    <header class="d-flex justify-content-between mb-3" v-if="showHeader">
      <div class="d-flex">
        <button class="btn btn-accent-outline" @click="gotoToday()">
          <i class="fa fa-calendar mr-2" />
          Today
        </button>

        <span>
          <el-date-picker
            v-model="selectedDay"
            format="MMMM dd yyyy"
            type="date"
            placeholder="Pick a day"
          />
        </span>

        <week-pager @prev-week="prevWeek" @next-week="nextWeek"> </week-pager>
      </div>

      <div>
        <slot />
      </div>
    </header>
    <div class="">
      <section class="d-flex overflow-auto">
        <div
          v-for="day in week"
          :key="day.toISOString()"
          class="card p-2 day-card"
          :class="{ today: isToday(day), selected: isSelectedDate(day) }"
        >
          <day-header :day="day" class="pb-4" />

          <slot :name="getISODate(day)">
            <div>N/D</div>
          </slot>
        </div>
      </section>
    </div>
  </div>
</template>

<script>
import DayHeader from "./day-header.vue";
import WeekPager from "./controls.vue";

export default {
  components: {
    DayHeader,
    WeekPager
  },
  props: {
    value: {
      type: Array,
      required: true
    },
    firstDayOfWeek: {
      type: Number,
      default: 0
    },
    outerDate: {
      type: Date,
      default: new Date()
    },
    showHeader: Boolean
  },
  data() {
    return {
      week: [],
      today: new Date(),
      selectedDay: new Date()
    };
  },
  watch: {
    week: {
      handler() {
        this.$emit("input", this.week);
      },
      deep: true,
      immediate: true
    },

    outerDate(date) {
      this.selectedDay = date;
    },

    selectedDay(selectedDay) {
      this.week = this.getWeek(new Date(selectedDay));
      this.$emit("date-changed", selectedDay);
    }
  },

  created() {
    this.week = this.getWeek(new Date());
    this.$emit("input", this.week);
  },

  methods: {
    getWeek(date) {
      const firstDate = new Date(
        date.setDate(date.getDate() - date.getDay() + this.firstDayOfWeek)
      );
      const week = [new Date(firstDate)];
      while (
        firstDate.setDate(firstDate.getDate() + 1) &&
        firstDate.getDay() !== this.firstDayOfWeek
      ) {
        week.push(new Date(firstDate));
      }
      return week;
    },

    isSelectedDayInWeek() {
      const isSelectedDayInWeek = this.week
        .map(date => this.getISODate(date))
        .includes(this.getISODate(this.selectedDay));
      if (!isSelectedDayInWeek) {
        this.selectedDay = this.week[0];
      }
    },

    gotoToday() {
      this.selectedDay = new Date();
    },

    nextWeek() {
      this.week = this.getWeek(
        new Date(this.week[6].setDate(this.week[6].getDate() + 1))
      );
      this.isSelectedDayInWeek();
    },

    prevWeek() {
      this.week = this.getWeek(
        new Date(this.week[0].setDate(this.week[0].getDate() - 1))
      );
      this.isSelectedDayInWeek();
    },

    getISODate(date) {
      return date.toISOString().slice(0, 10);
    },

    isToday(date) {
      return this.getISODate(new Date()) == this.getISODate(date);
    },

    isSelectedDate(date) {
      return this.getISODate(this.selectedDay) == this.getISODate(date);
    }
  }
};
</script>

<style lang="scss" scoped>
header .btn,
input,
span {
  margin-right: 10px;
}

header .btn {
  height: 37px;
  align-items: center;
}

.day-card {
  background: #F4F5F7;
  transition: all ease 0.3s;
  height: 100%;
  min-height: 600px;
  width: 270px;
  min-width: 270px;
  border: 2px solid transparent;
  margin: 0 7px;

  &:first-child {
    margin-left: 0;
  }

  &:last-child {
    margin-right: 0;
  }

  &.today {
    background: #f6f7f9;
  }

  &.selected,
  &:hover {
    border: 2px solid #ddd;
  }
}
</style>
