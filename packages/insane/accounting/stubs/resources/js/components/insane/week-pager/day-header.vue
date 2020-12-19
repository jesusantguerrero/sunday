<template>
  <div>
    <div class="d-flex justify-content-between pt-2">
      <span :class="{ today: isToday }"
        ><b> {{ dayName }} </b></span
      >
      <span class="bolder"> {{ dateMonth }} </span>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    day: {
      type: Date,
      required: true
    }
  },
  computed: {
    today() {
      return new Date();
    },

    isToday() {
      return this.getISODate(this.today) == this.getISODate(this.day);
    },

    dayName() {
      const days = [
        "Domingo",
        "Lunes",
        "Martes",
        "Wiercoles",
        "Jueves",
        "Viernes",
        "Sabado"
      ];
      return !this.isToday ? days[this.day.getDay()] : "Today";
    },

    dateMonth() {
      return `${this.day.getMonth() + 1} / ${this.day.getDate()}`;
    }
  },
  methods: {
    getISODate(date) {
      return date.toISOString().slice(0, 10);
    }
  }
};
</script>

<style lang="scss" scoped>
.today {
  color: var(--primary-color);
  font-weight: bolder;
}
</style>
