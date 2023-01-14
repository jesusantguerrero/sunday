<template>
    <div>
        <div class="toggle-show__container">
            <div>
                <button
                    class="text-button"
                    v-if="isToday"
                    @click="toggleViewAll"
                >
                    {{ textToggleMostrar }}
                </button>
            </div>
        </div>

        <grid-item
            v-for="(daySlot, index) in formattedAgenda"
            :key="`${daySlot.horaExacta}-${index}`"
            :day-slot="daySlot"
            :show-all="showAll"
            :link-fields="linkFields"
            :current-time="currentTime"
            :allow-delete="allowDelete"
            :allow-update="allowUpdate"
            @update-called="$emit('update-called', $event)"
            @delete-called="$emit('delete-called', $event)"
        />
    </div>
</template>

<script>
import { isToday, format } from "date-fns";
import GridItem from "./grid-item.vue";
const showEmptySlots = false;

export default {
    components: {
        GridItem
    },
    props: {
        selectedDate: {
            type: Date,
            required: true
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
            required: true
        },
        timeField: {
            type: String,
            default: "time"
        },
        dateField: {
            type: String,
            default: "date"
        },
        timeEndField: {
            type: String,
            default: "end_time"
        },
        dateEndField: {
            type: String,
            default: "due_date"
        },
        linkFields: {
            type: Object,
                default() {

            }
        },
    },
    data() {
        return {
            currentTime: new Date(),
            agenda: [],
            modoManual: false,
            showAll: false
        };
    },
    created() {
        setInterval(() => {
            this.currentTime = new Date();
        }, 1000);
    },
    computed: {
        horaActual() {
            if (this.modoManual) {
                return this.horaActiva;
            } else {
                return new Date().getHours();
            }
        },

        isToday() {
            return isToday(this.selectedDate);
        },

        textToggleMostrar() {
            return this.showAll ? "Current" : "Past Events";
        },

        iconToggleMostrar() {
            return this.showAll ? "Current" : "Past Events";
        },

        formattedAgenda() {
            const agenda = [];
            let programaAnterior = {};
            for (let hora = 0; hora < 24; hora++) {
                let horaString = hora;
                let horaProxima = hora + 1;

                if (hora < 10) {
                    horaString = "0" + horaString;
                }

                if (horaProxima < 10) {
                    horaProxima = "0" + horaProxima;
                }

                if (horaProxima == 24) {
                    horaProxima = "00";
                }

                const programas = this.schedule.filter(programa => {
                    let time = programa[this.timeField]?.slice(0, 2);
                    time = time || 0;
                    return time == horaString;
                });

                const date = new Date();
                const lastIndex = programas.length - 1;
                const lastHour = lastIndex >= 0 ? programas[lastIndex][this.timeField] : programaAnterior.horaFinal || horaProxima + ":00";
                const current = lastHour?.split(":");

                const proximoPrograma = this.schedule.find(program => {
                    date.setHours(current ? current[0] : 0);
                    date.setMinutes(current ? current[1] : 0);
                    date.setSeconds(0);

                    return this.isHourBetween(
                        program.hora,
                        program.hora_final,
                        date,
                        true,
                        "aqui"
                    );
                });

                const horaExacta = `${hora}:00`;

                if (!programas.length && showEmptySlots) {
                    const programa = {
                        hora: programaAnterior.hora_final
                            ? programaAnterior.hora_final.slice(0, 5)
                            : hora,
                        horaExacta: programaAnterior.hora_final
                            ? programaAnterior.hora_final.slice(0, 5)
                            : horaExacta,
                        descripcion: "Sin descripcion ",
                        horaFinal: proximoPrograma
                            ? proximoPrograma.hora.slice(0, 5)
                            : horaProxima + ":00",
                        nombre: `N / A`
                    };

                    if (programa.hora != programa.horaFinal) {
                        programaAnterior = programa;
                        agenda.push(programa);
                    }
                } else {
                    programas.forEach(programa => {
                        programa[this.timeField] = programa[this.timeField]
                            ? programa[this.timeField].slice(0, 5)
                            : "00:00";
                        if (programa[this.timeEndField]) {
                            programa[this.timeEndField] = programa[
                                this.timeEndField
                            ].slice(0, 5);
                        }
                        agenda.push(programa);
                        programaAnterior = programa;
                    });
                }
            }

            return agenda;
        }
    },
    methods: {
        hourHasPassed(programa) {
            if (typeof programa.hora == "string" && programa.horaFinal) {
                const segundahoraString = programa.horaFinal
                    .replaceAll(":", "")
                    .slice(0, 4);
                const horaActualString = format(this.currentTime, "HHmm");
                return (
                    segundahoraString < horaActualString &&
                    this.isToday &&
                    !this.showAll
                );
            } else {
                const hours = programa.hora;
                return (
                    Number(hours) < this.horaActual &&
                    this.isToday &&
                    !this.showAll
                );
            }
        },
        toggleViewAll() {
            this.showAll = !this.showAll;
        },

        isHourBetween(horaInicial = "", horaFinal = "", fechaActual, bigger) {
            if (horaInicial && horaFinal && fechaActual) {
                const primerahoraString = horaInicial.replace(":", "");
                const segundahoraString = horaFinal.replace(":", "");
                const horaActualString = format(fechaActual, "HHmm");
                return (
                    (!bigger
                        ? horaActualString >= primerahoraString
                        : primerahoraString > horaActualString) &&
                    horaActualString <= segundahoraString
                );
            }
            return false;
        }
    }
};
</script>

<style lang="scss">
.past-hour {
    display: none;
}

.image_cover {
    @apply bg-purple-400 text-white rounded-full font-bold;
    width: 36px;
    height: 36px;
    min-width: 36px;
    min-height: 36px;
    display: flex;
    align-items: center;
    justify-content: center;
    text-transform: uppercase;
    overflow: hidden;
}

.on-air-program {
    padding: 0 10px;
    .image_cover {
        font-size: 50px;
        width: 132px;
        min-width: 132px;
        height: 132px;
        border-radius: 14px;
    }
}

.toggle-show__container {
    @apply flex justify-end;
    margin: 10px 0;
    min-height: 10px;
}

.text-button {
    @apply font-medium text-blue-600 border-2 border-gray-400 bg-white;
    width: 297px;
    height: 47px;
    font-size: 16px;
    text-align: left;
    padding-left: 28px;
    border-radius: 9px;
    color: #929497;
    box-shadow: 4px 4px 4px rgba(255, 255, 255, 0.5);

    &:focus {
        outline: none;
    }
}

@media (max-width: 768px) {
    .toggle-show__container {
        @apply justify-center w-full;
        div,
        button {
            @apply w-full;
        }
    }
}
</style>
