<template>
  <div
    class="w-full overflow-hidden"
  >
    <div class="w-full ">
      <controls :value="value" @input="$emit('input', $event)"></controls>
      <grid
        :selected-date="value"
        :schedule="schedule"
        :allow-add="allowAdd"
        :link-fields="linkFields"
        :allow-delete="allowDelete"
        :allow-update="allowUpdate"
        @delete-called="deleteItem"
        @update-called="editItem"
      >
      </grid>
    </div>
  </div>
</template>

<script>
import { format } from "date-fns";
import Controls from "./controls";
import Grid from "./grid";
import axios from "axios";

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
      selectedDay: null,
      horaActiva: 0,
      modoManual: false,
      mostrarTodos: false,
    };
  },
  created() {
    setInterval(() => {
      this.currentTime = new Date();
    }, 1000);
  },
  computed: {
    horaActual() {
      return new Date().getHours();
    },
    currentProgram() {
      return this.schedule.find(program => {
        return this.isHourBetween(
          program.hora,
          program.hora_final,
          this.currentTime
        );
      });
    },
    nextProgram() {
      return {};
    }
  },
  methods: {
    isHourBetween(horaInicial = "", horaFinal = "", fechaActual) {
      const primerahoraString = horaInicial.replace(":", "");
      const segundahoraString = horaFinal.replace(":", "");
      const horaActualString = format(fechaActual, "HHmm");
      return (
        horaActualString >= primerahoraString &&
        horaActualString <= segundahoraString
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
