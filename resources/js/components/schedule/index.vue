<template>
  <div
    class="w-full overflow-hidden"
  >
    <div class="w-full ">
      <Controls :value="value" @input="$emit('input', $event)"/>
      <Grid
        :selected-date="value"
        :schedule="schedule"
        :allow-add="allowAdd"
        :link-fields="linkFields"
        :allow-delete="allowDelete"
        :allow-update="allowUpdate"
        @delete-called="deleteItem"
        @update-called="editItem"
      />
    </div>
  </div>
</template>

<script>
import { format } from "date-fns";
import Controls from "./controls";
import Grid from "./grid";

export default {
  props: {
    allowAdd: {
      type: Boolean,
      default: false
    },
    allowUpdate: {
      type: Boolean,
      default: false
    },
    allowDelete: {
      type: Boolean,
      default: false
    },
    schedule: {
        type: Array,
        default() {
            return []
        }
    },
    linkFields: {
        type: Object,
        default() {

        }
    },
    value: {
        type: [Date, null],
        required: true
    }
  },
  components: {
    Controls,
    Grid
  },
  data() {
    return {
      formData: {},
      currentTime: new Date(),
    };
  },
  created() {
    setInterval(() => {
      this.currentTime = new Date();
    }, 1000);
  },
  computed: {
    currentHour() {
      return new Date().getHours();
    },
    currentItem() {
      return this.schedule.find(program => {
        return this.isHourBetween(
          program.hora,
          program.hora_final,
          this.currentTime
        );
      });
    },
    nextItem() {
      return {};
    }
  },
  methods: {
    isHourBetween(horaInicial = "", horaFinal = "", fechaActual) {
      const primerahoraString = horaInicial.replace(":", "");
      const segundahoraString = horaFinal.replace(":", "");
      const currentHourString = format(fechaActual, "HHmm");
      return (
        currentHourString >= primerahoraString &&
        currentHourString <= segundahoraString
      );
    },

    deleteItem(item) {
        this.$emit("delete", item)
    },

    editItem(item) {
        this.$emit("edit", item)
    }
  }
};
</script>
