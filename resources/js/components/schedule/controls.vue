<template>
  <div class="controls">
    <div class="pl-8 font-bold capitalize month-name">
        {{ getMonthName(selectedDay)}}
    </div>
    <div class="controls-container">
      <div class="flex justify-start w-full">
        <div class="day-controls" @click.prevent="prevWeek()">
          <i class="fa fa-chevron-left"></i>
        </div>
      </div>

      <div
        v-for="day in week"
        :key="`item-${day}`"
        class="flex justify-center w-full">
        <div
          class="day-item"
          :class="{ 'selected-date': isSelectedDate(day) }"

          @click="selectedDay = day"
        >
          <span class="block text-xl font-bold">{{ getDateLabel(day) }}</span>
          <span class="capitalize">{{ getDayName(day) }}</span> <br />
        </div>
      </div>

      <div class="flex justify-end w-full">
        <div class="day-controls" @click.prevent="nextWeek()">
          <i class="fa fa-chevron-right"></i>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { format } from "date-fns";
import { isSameDate } from "../../utils";

export default {
  name: "Controls",
  props: {
    value: {
      type: Date,
    },
  },
  data() {
    return {
      week: [],
      today: new Date(),
      selectedDate: this.value || new Date(),
      firstDayOfWeek: 0,
    };
  },
  watch: {
    selectedDate: {
      handler(selectedDate) {
        if (this.value && selectedDate && !isSameDate(this.value, selectedDate)) {
          this.$emit("input", selectedDate);
        }
      },
      immediate: true,
    },
  },
  mounted() {
    this.setWeek();
  },
  methods: {
    setWeek() {
      this.week = this.getWeek(this.selectedDate);
    },
    getWeek(originalDate) {
      const date = new Date(originalDate);
      const firstDate = new Date(date.setDate(date.getDate() - 4));
      const week = [];
      for (let i = 0; i < 7; i++) {
        firstDate.setDate(firstDate.getDate() + 1);
        week.push(new Date(firstDate));
      }
      return week;
    },
    setSelectedDateInWeek() {
      this.selectedDate = this.week[3];
    },
    gotoToday() {
      this.selectedDate = new Date();
      this.todayMode = !this.todayMode;
    },
    nextWeek() {
      const date = new Date(this.selectedDate);
      this.week = this.getWeek(new Date(date.setDate(date.getDate() + 1)));
      this.setSelectedDateInWeek();
    },
    prevWeek() {
      const date = new Date(this.selectedDate);
      this.week = this.getWeek(new Date(date.setDate(date.getDate() - 1)));
      this.setSelectedDateInWeek();
    },
    getISODate(date) {
      return format(date, "yyyy-MM-dd");
    },
    isToday(date) {
      return this.getISODate(new Date()) == this.getISODate(date);
    },
    isSelectedDate(date) {
      return this.getISODate(this.selectedDate) == this.getISODate(date);
    },
    getDayName(date) {
      return format(date, "iii");
    },
    getMonthName(date) {
      return format(date, "MMM, yyyy");
    },
    getDateLabel(date) {
      return format(date, "dd");
    },
  },
};
</script>

<style lang="scss" scoped>
$primary-color: var(--primary-color);

.controls {
  @apply text-left bg-white mb-1 p-2;
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
  border-radius: 0.8rem;
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

.selected-date {
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
