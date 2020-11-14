<template>
  <div class="controls">
    <div class="month-name pl-8 font-bold capitalize">
        {{ getMonthName(selectedDay)}}
    </div>
    <div class="controls-container">
      <div class="w-full flex justify-start">
        <div class="day-controls" @click.prevent="prevWeek()">
          <i class="fa fa-chevron-left"></i>
        </div>
      </div>

      <div
        v-for="day in week"
        :key="`item-${day}`"
        class="w-full flex justify-center">
        <div
          class="day-item"
          :class="{ 'selected-day': isSelectedDate(day) }"

          @click="selectedDay = day"
        >
          <span class="text-xl font-bold block">{{ getDateLabel(day) }}</span>
          <span class="capitalize">{{ getDayName(day) }}</span> <br />
        </div>
      </div>

      <div class="w-full flex justify-end">
        <div class="day-controls" @click.prevent="nextWeek()">
          <i class="fa fa-chevron-right"></i>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { format } from "date-fns";

export default {
  name: "Controls",
  props: {
    value: {
      type: Date
    }
  },
  data() {
    return {
      week: [],
      today: new Date(),
      selectedDay: new Date(),
      firstDayOfWeek: 0
    };
  },
  watch: {
    selectedDay: {
      handler() {
        this.$emit("input", this.selectedDay);
      },
      immediate: true
    },
    value: {
      handler(newDate) {
        this.selectedDay = newDate || this.selectedDay;
      },
      immediate: true
    }
  },
  created() {
    this.week = this.getWeek(new Date());
  },
  methods: {
    getWeek(date) {
      const firstDate = new Date(date.setDate(date.getDate() - 4));
      const week = [];
      for (let i = 0; i < 7; i++) {
        firstDate.setDate(firstDate.getDate() + 1);
        week.push(new Date(firstDate));
      }
      return week;
    },
    isSelectedDayInWeek() {
      this.selectedDay = this.week[3];
    },
    gotoToday() {
      this.selectedDay = new Date();
      this.todayMode = !this.todayMode;
    },
    nextWeek() {
      this.week = this.getWeek(
        new Date(this.selectedDay.setDate(this.selectedDay.getDate() + 1))
      );
      this.isSelectedDayInWeek();
    },
    prevWeek() {
      this.week = this.getWeek(
        new Date(this.selectedDay.setDate(this.selectedDay.getDate() - 1))
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
    },
    getDayName(date) {
      return format(date, "iii");
    },
    getMonthName(date) {
      return format(date, "MMM, yyyy");
    },
    getDateLabel(date) {
      return format(date, "dd");
    }
  }
};
</script>

<style lang="scss" scoped>
$primary-color: var(--primary-color);

.controls {
  @apply text-left bg-white mb-1 p-2 border-gray-100 border-2;
  border-radius: 12px;
}

.controls-container {
  @apply grid grid-cols-3 pb-3;
  user-select: none;
}

.day-item,
.day-controls {
  @apply text-center capitalize text-gray-600 py-2 border-2 cursor-pointer w-20 border-white;
  transition: all ease 0.3s;
  border-radius: 0.80rem;
  display: none;

  &:hover {
    @apply text-purple-400;
  }
}

.day-controls {
  @apply flex justify-center items-center text-gray-700;
   &:hover {
    @apply bg-gray-400 text-gray-700;
  }
}

.selected-day {
  @apply visible text-purple-400 shadow-lg border-purple-400 border-2;
  display: block;
}

@media (min-width: 768px) {
  .controls-container {
    @apply grid-cols-9;
    user-select: none;
  }

  .day-item {
    display: block;
  }
}
</style>
