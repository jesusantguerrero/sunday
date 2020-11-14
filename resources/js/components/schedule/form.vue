<template>
  <div class="wrap bg-white">
    <div class="container-fluid">
      <form action="" class="row" @submit.prevent="saveProgram">
        <div class="col-12">
          <article class="prose lg:prose-xl mb-5">
            <h3>{{ title }}</h3>
          </article>
        </div>
        <div class="form-group col-md-12">
          <label for=""> Nombre: </label>
          <input
            type="text"
            class="my-form-control"
            v-model="formData.nombre"
            required
          />
        </div>

        <div class="flex">
          <div class="form-group w-1/4 mr-2">
            <label class="demonstration">Fecha:</label>
            <el-date-picker
              v-model="formData.fecha"
              type="date"
              input-class="w-full"
              placeholder="selecciona una fecha"
            >
            </el-date-picker>
          </div>

          <div class="form-group w-1/4 mx-2">
            <label for="hora"> Hora Inicial: </label>
            <el-time-picker
              v-model="formData.hora"
              required
              placeholder="selecciona una hora"
              format="hh:mm a"
              :editable="false"
              :picker-options="{
                start: '00:00',
                step: '00:15',
                end: '23:45',
              }"
            >
            </el-time-picker>
          </div>

          <div class="form-group w-1/4 mx-2">
            <label for="dia"> Hora Final: </label>
            <el-time-picker
              :editable="false"
              :disabled="!formData.hora"
              v-model="formData.hora_final"
              placeholder="selecciona una hora final"
              format="hh:mm a"
              :picker-options="{
                start: '00:00',
                step: '00:15',
                end: '23:45',
              }"
            >
            </el-time-picker>
          </div>

          <div class="form-group w-1/4 ml-2" v-if="isMinorHour">
            <label class="demonstration">Fecha Final:</label>
            <el-date-picker
              v-model="formData.fecha_final"
              type="date"
              required
              input-class="w-full"
              placeholder="selecciona una fecha"
            >
            </el-date-picker>
          </div>
        </div>

        <div class="form-group col-12">
          <label for=""> URL imagen: </label>
          <input
            v-model="formData.url_imagen"
            name="url_imagen"
            id="url_imagen"
            class="my-form-control"
          />
        </div>

        <div class="form-group col-12">
          <div>
            <label for=""> Frecuencia: </label>
            <select
              name=""
              id=""
              v-model="formData.frecuencia"
              class="my-form-control"
            >
              <option value=""> No Repetir</option>
              <!-- <option value="DAILY">Repetir cada dia</option> -->
              <option value="WEEK_DAY"
                >Repetir los <span class="capitalize">{{ day }}</span></option
              >
              <option value="WEEK">Cada Semana</option>
            </select>
          </div>
        </div>

        <div class="form-group col-12" v-if="formData.frecuencia == 'WEEK'">
          <div>
            <label for=""> Se Repite el: </label>
            <div class="flex grid-cols-7">
              <label
                :for="day"
                v-for="(day, index) in weekDays"
                :key="day"
                class="mr-5"
              >
                <input
                  type="checkbox"
                  :name="index"
                  :value="index"
                  :id="day"
                  class="mr-1"
                  v-model="formData.week_days"
                />
                {{ day }}
              </label>
            </div>
          </div>
        </div>

        <div class="flex">
          <div class="form-group w-1/2" v-if="formData.frecuencia">
            <div>
              <label for=""> Termina: </label>
              <!-- <label for="termina-date">
                <input
                  type="radio"
                  name="termina-date"
                  id="termina-date"
                  value="date"
                  v-model="formData.limit_type"
                />
                Hasta el
              </label> -->

              <label for="termina-count">
                <input
                  type="radio"
                  name="termina-count"
                  id="termina-count"
                  value="count"
                  v-model="formData.limit_type"
                />
                Despues de
              </label>
            </div>
          </div>

          <div class="items-center w-1/2" v-if="formData.limit_type == 'count'">
            <label class="limit">Despues de:</label>
            <div class="flex items-center">
              <input
                v-model="formData.limite"
                required
                type="number"
                :min="2"
                class="my-form-control"
                placeholder="52"
              />
              <label for="" class="ml-10"> Veces</label>
            </div>
          </div>
        </div>

        <div class="form-group col-12">
          <label for=""> Descripcion: </label>
          <textarea
            required
            v-model="formData.descripcion"
            name="descripcion"
            id="descripcion"
            class="my-form-control"
            cols="30"
            rows="5"
          ></textarea>
        </div>

        <div class="flex justify-end">
          <button
            type="submit"
            v-if="formData.id"
            class="btn bg-red-500 mr-4"
            @click.prevent="deleteEvent(formData)"
          >
            Eliminar
          </button>
          <button
            type="submit"
            class="btn bg-gray-500"
            @click.prevent="$emit('canceled')"
          >
            Cancelar
          </button>

          <button
            type="submit"
            class="btn btn-primary ml-4"
            :disabled="isLoading"
          >
            {{ saveButtonText }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import { format, toDate, addDays, isBefore } from "date-fns";
// import { validationMixin, required } from "vuelidate";
import { es } from "date-fns/locale/";
import axios from "axios";
import { MessageBox } from "element-ui";

const APPLY_RECURRENT = 1;
const NOT_RECURRENT = 1;
const CANCEL = -1;
const defaultForm = {
  nombre: "",
  fecha: new Date(),
  hora: new Date(),
  week_days: [],
  frecuencia: "",
  hora_final: "",
  descripcion: "",
  limit_type: "",
  limite: 0,
};

export default {
  props: {
    endpoint: {
      type: String,
      default: "http://mahanahim.test/wp-json/ic-schedule/v1/programs",
    },
    data: {
      type: Object,
      default() {
        return {};
      },
    },
  },
  computed: {
    title() {
      return this.formData.id
        ? `Actualizar ${this.formData.nombre}`
        : "Agregar Nuevo Programa";
    },
    saveButtonText() {
      return this.formData.id ? "Guardar Cambios" : "Agregar Programa";
    },

    isMinorHour() {
      return (
        this.formData.hora_final &&
        isBefore(this.formData.hora_final, this.formData.hora)
      );
    },

    day() {
      return format(this.formData.fecha, "iiii", { locale: es });
    },
  },
  watch: {
    data: {
      handler() {
        if (this.data && Object.keys(this.data).length) {
          const formData = { ...this.data };
          if (this.data.fecha) {
            formData.fecha = this.setDate(formData.fecha);
            formData.fecha_final =
              this.setDate(formData.fecha_final) || formData.fecha;
            formData.fecha_limite = this.setDate(formData.fecha_limite);
            formData.hora = this.setTime(this.data.hora);
            formData.hora_final = this.setTime(this.data.hora_final);
          }
          formData.week_days = formData.week_days
            ? formData.week_days.split(",")
            : [];
          this.formData = { ...formData };
          this.preEditData = { ...formData };
        }
      },
      immediate: true,
    },
    "formData.frecuencia"(value, oldValue) {
      if (!value) {
        this.formData.limit_type = "";
      } else if (!oldValue && !this.formData.limit_type) {
        this.formData.limit_type = "date";
      }
    },
    isMinorHour: {
      handler(isMinorHour) {
        this.$set(
          this.formData,
          "fecha_final",
          isMinorHour ? addDays(this.formData.fecha, 1) : this.formData.fecha
        );
      },
    },
  },
  data() {
    return {
      isLoading: false,
      weekDays: ["DOM", "LUN", "MAR", "MIE", "JUE", "VIE", "SAB"],
      preEditData: {},
      formData: { ...defaultForm },
    };
  },
  methods: {
    setTime(data) {
      const date = new Date();
      const hourDate = data.split(":");
      date.setHours(hourDate[0]);
      date.setMinutes(hourDate[1]);
      date.setSeconds(0);
      return date;
    },

    setDate(dateValue) {
      const date = dateValue ? dateValue.split("-") : null;
      return date ? toDate(new Date(date[0], date[1] - 1, date[2])) : null;
    },

    clearForm() {
      this.formData = { ...defaultForm };
    },

    prepareData() {
      const formData = { ...this.formData };
      formData.fecha = format(formData.fecha, "yyyy-MM-dd");
      formData.hora = format(formData.hora, "HH:mm:ss");
      formData.hora_final = format(formData.hora_final, "HH:mm:ss");
      if (formData.fecha_final) {
        formData.fecha_final = format(formData.fecha_final, "yyyy-MM-dd");
      }
      return formData;
    },

    isFormValid() {
      this.$v.$touch();
      return !this.$v.$invalid;
    },

    confirmEditMasive(isDelete) {
      const vars = {
        title: "Aplicar cambios a recurrentes?",
        confirmButtonText: "Aplicar Cambio Recurrente",
        cancelButtonText: "Aplicar solo a este",
      };
      if (isDelete) {
        vars.title = "Aplicar borrado recurrente?";
        vars.confirmButtonText = "Borrar recurrente";
        vars.cancelButtonText = "Borrar solo este";
      }
      return MessageBox.confirm(vars.title, "Warning", {
        distinguishCancelAndClose: true,
        confirmButtonText: vars.confirmButtonText,
        cancelButtonText: vars.cancelButtonText,
        type: "warning",
      })
        .then(() => {
          return APPLY_RECURRENT;
        })
        .catch((action) => {
          return action === "cancel" ? NOT_RECURRENT : CANCEL;
        });
    },

    validateForm() {
      const { hora, hora_final, fecha } = this.formData;
      const isValid = [hora, fecha, hora_final].every((val) => val);
      if (!isValid) {
        this.$notify({
          type: "info",
          message: `Llena todas las fechas`,
        });
      }
      return isValid;
    },

    async saveProgram() {
      if (!this.validateForm()) {
        return;
      }

      if (this.endpoint && !this.isLoading) {
        this.isLoading = true;
        const params = new URLSearchParams();
        Object.entries(this.prepareData()).forEach(([name, value]) => {
          if (value) {
            params.append(name, value);
          }
        });

        const url = this.formData.id
          ? `${this.endpoint}/${this.formData.id}`
          : this.endpoint;
        const method = this.formData.id ? "PUT" : "POST";

        if (this.preEditData.frecuencia) {
          const recurrent = await this.confirmEditMasive();
          if (recurrent === CANCEL) {
            this.isLoading = false;
            return;
          }
          params.append("recurrent", recurrent);
        }

        if (this.formData.frecuencia) {
          params.append("recurrent", 1);
        }

        axios({
          url,
          method,
          data: params,
        })
          .then(({ data }) => {
            this.$notify({
              type: "success",
              message: `${data.nombre} Guardado con exito!`,
            });
            this.clearForm();
            this.$emit("saved");
          })
          .catch((err) => {
            this.$notify({
              type: "error",
              message: err.toString(),
            });
          })
          .finally(() => {
            this.isLoading = false;
          });
      } else {
        this.$notify({
          type: "error",
          message: "No hay endpoint valido",
        });
      }
    },

    async deleteEvent() {
      if (this.preEditData.frecuencia) {
        const isMassive = await this.confirmEditMasive(true);
        if (isMassive !== CANCEL) {
          this.sendDelete(isMassive);
        }
        return;
      }

      MessageBox.confirm(
        "Este programa se eliminará",
        "Eliminar programa"
      ).then((action) => {
        if (action == "confirm") {
          this.sendDelete();
        }
      });
    },

    sendDelete(isMassive) {
      const params = isMassive ? "?recurrent=true" : "";
      axios({
        url: `${this.endpoint}/${this.formData.id}${params}`,
        method: "delete",
        data: {
          delete_related: true,
        },
      })
        .then(() => {
           this.$emit("saved");
        })
        .catch(() => {});
    },
  },
};
</script>

<style lang="scss">
.form-group {
  @apply mb-5;
  label {
    @apply block text-gray-700 text-sm font-bold mb-2;
  }
}

.my-form-control {
  @apply shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight;

  &:focus {
    @apply outline-none shadow-outline;
  }
}

.btn {
  @apply bg-green-500 text-white px-5 py-2;
  border-radius: 14px;
}

.el-date-editor.el-input {
  width: 100%;
}
</style>
