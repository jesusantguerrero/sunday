<template>
  <div class="appointment-item">
    <div class="appointment-item__header">
      <div class="appointment-item__patient"> Jesus Guerrero</div>
    </div>
    <div class="apointment-item__body p-3 text-left">
     <div>
        <span> Hora:</span>
        <h6 class="calendar-author bolder">{{ show.hour }} - {{ show.hour }}</h6>
      </div>
      <div>
        <h6 class="calendar-author bolder">{{ show.status }}</h6>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    show: {
      type: Object,
      required: true
    }
  },
  computed: {
    statusIcon() {
      const icons = [
        "fas fa-exclamation-circle",
        "fas fa-check-circle",
        "fas fa-exclamation-circle"
      ];
      return icons[this.show.status || 0];
    },
    statusIconColor() {
      const colors = ["text-warning", "text-success", "text-danger"];
      return colors[this.show.status || 0];
    }
  }
};
</script>

<style lang="scss" scoped>
.appointment-item {
  background: white;
  border-radius: 4px;
  overflow: hidden;
  margin-bottom: 15px;
  border: 1px solid #fefefe;

  &__header {
    height: 40px;
    border-bottom: 1px solid #dedede;
    margin: 0 5px;
    display: flex;
    align-items: center;
    padding: 15px;
    justify-content: space-between;
  }

  &__patient {
    font-weight: bolder;
  }
}

.bolder {
  font-weight: bolder;
}

</style>
