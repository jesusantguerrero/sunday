<template>
  <div
    class="w-full overflow-hidden"
  >
    <div class="w-full ">
      <controls v-model="diaActivo" @input="diaActivo = $event"></controls>
      <grid
        :selected-date="diaActivo"
        :schedule="schedule"
        :allow-add="allowAdd"
        :allow-delete="allowDelete"
        :allow-update="allowUpdate"
        @delete-called="deleteItem"
        @update-called="openForm"
        @add-called="openForm"
      >
      </grid>
    </div>
  </div>
</template>

<script>
import { format } from "date-fns";
import Controls from "./controls";
import ScheduleForm from "./form";
import Grid from "./grid";
import axios from "axios";
import { MessageBox } from "element-ui";

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
    }
  },
  components: {
    Controls,
    Grid,
    ScheduleForm
  },
  data() {
    return {
      msg: "Horario Mahanahim",
      isFormOpen: false,
      formData: {},
      currentTime: new Date(),
      diaActivo: new Date(),
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

    openForm(formData) {
      this.formData = formData;
      this.isFormOpen = true;
    },

    onSaved(item) {
      this.getSchedules();
      this.isFormOpen = false;
      this.formData = {};
      console.log(item);
    },

    onCanceled() {
      this.isFormOpen = false;
      this.formData = {};
    },

    deleteItem(item) {
      MessageBox.confirm(
        "Este programa se eliminará",
        "Eliminar programa"
      ).then(action => {
        if (action == "confirm") {
          axios({
            url: `${this.endpoint}/${item.id}`,
            method: "delete",
            data: {
              delete_related: true
            }
          })
            .then(() => {
              this.getSchedules();
            })
            .catch(() => {});
        }
      });
    }
  }
};
</script>

<style lang="scss">
:root {
  --primary-color: #030371;
  --primary-color-7: rgba(3, 3, 113, 0.7);
  --primary-color-6: rgba(3, 3, 113, 0.6);
  --primary-color-5: rgba(3, 3, 113, 0.5);
  --primary-radius: 14px;
}
#app {
  font-family: "Avenir", Helvetica, Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
}
</style>
