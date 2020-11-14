<template>
  <div
    class="day-slot"
    :class="{
      'on-air-program': isOnAir,
      'past-hour': hasPassed
    }"
  >
    <div class="content-body">
      <div class="image_cover">
        <img :src="daySlot.url_imagen" alt="" />
      </div>
      <div class="program-name">
        <div class="badge-on-air flex items-center mb-1 w-24" v-if="isOnAir">
          Al aire
          <span
            class="rounded-full block live-dot bg-red-500 h-2 w-2 ml-2"
          ></span>
        </div>
        <div class="pl-1s">
          <h4 class="day-slot__title">
            <div class="">
              {{ daySlot.title }}
            </div>

            <!-- CRUD Controls  -->
            <div class="crud-controls">
              <span
                class="mx-2 px-2 cursor-pointer hover:text-blue-500"
                v-if="daySlot.id && allowUpdate"
                @click="$emit('update-called', daySlot)"
              >
                <i class="fa fa-edit"></i>
              </span>

              <span
                class="mx-2 px-2 cursor-pointer rounded transition-all hover:text-red-700"
                v-if="daySlot.id && allowDelete"
                @click="$emit('delete-called', daySlot)"
              >
                <i class="fa fa-trash"></i>
              </span>
            </div>
            <!-- End of CRUD controls -->
          </h4>
        </div>
        <div class="day-slot__description pl-1">
          {{ daySlot.descripcion && daySlot.descripcion.slice(0, 150) }}
          {{ daySlot.descripcion && daySlot.descripcion.length > 150 ? "..." : "" }}
        </div>
      </div>
    </div>

    <div class="hours-container">
      <div class="hours-container__left">
        <div class="hours-container__divider"></div>
        <div class="hours-container__item">
          <span class="block" v-if="isOnAir"> De</span>
          <span class="block"> {{ formatHour(daySlot[this.timeField]) }}</span>
          <span class="block" v-if="isOnAir"> A </span>
          <span class="block mx-2" v-else> - </span>
          <span class="block"> {{ formatHour(daySlot[this.timeEndField]) }} </span>
        </div>

      </div>
      <span class="ml-3" v-if="!isOnAir">
        <i class="fas fa-chevron-down"></i>
      </span>
    </div>
  </div>
</template>

<script>
import { format } from "date-fns";

export default {
  props: {
    daySlot: {
      type: Object,
      required: true
    },
    hasPassed: {
      type: Boolean,
      default: false
    },
    isOnAir: {
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
    timeField: {
        type: String,
        default: 'time'
    },
    dateField: {
        type: String,
        default: 'date'
    }
  },
  methods: {
    formatHour(hour) {
        if (hour) {
            const date = new Date();
            const time = hour.split(":");
            date.setHours(time[0]);
            date.setMinutes(time[1]);
            return format(date, "hh:mm a");
        }
    }
  }
};
</script>

<style lang="scss">
.day-slot {
  @apply flex justify-between border-gray-200 border-2 shadow-sm;
  position: relative;
  border-radius: var(--primary-radius);
  background: white;
  box-shadow: 4px 8px 8px rgba(255, 255, 255, 0.5);
  height: 80px;
  margin-bottom: 8px;
  padding: 15px  12px 16px 12px;

  &::before {
    @apply bg-blue-400 rounded-lg;
    content: "";
    left: -3px;
    position: absolute;
    top: 25px;
    width: 5px;
    height: 20px;
  }
}

.badge-on-air {
  font-size: 17px;
  height: 25px;
  width: 84px;
  color: #231f20 !important;
  font-family: "Poppins", sans-serif;
  box-shadow: 4px 4px 4px rgba(255, 255, 255, 0.5);
}

.on-air-program {
  height: 152px !important;
  min-height: 152px !important;
  .content-body {
    color: white;
  }

  .program-name {
    margin-left: 25px;
  }

  .day-slot__title, .day-slot__description {
    margin-left: 5px;
    color: white ;
  }

  .hours-container {
    color: white;
  }

  .hours-container {
    justify-content: flex-start;

    &__divider {
      height: 67.796px;
      width: 1px;
      background: white;
    }

    &__item {
      margin-left: 35px;
      display: flex;
      flex-flow: column;
    }

  }
}

.content-body {
  @apply flex;
  color: var(--primary-color);
  align-items: center;
}

.day-slot__title {
  @apply flex items-center justify-between font-medium;
  margin-left: 10px;
  margin-bottom: 2px;
  font-size: 20px;
  color: #414042;
}

.program-name {
  // margin-top: 8px;
}

.day-slot__description {
  @apply font-light;
  margin-left: 10px;
  font-size: 16px;
}

.crud-controls {
  @apply flex justify-end items-center;
}

.hours-container {
  @apply flex items-center justify-between font-light;
  font-size: 15px;
  width: 250px;
  min-width: 250px;
  overflow: hidden;
  flex-direction: row !important;
  color: #414042;

  &__divider {
    height: 67.796px;
    width: 1px;
    background: #aaa;
  }

  &__left {
    display: flex !important;
    align-items: center;
  }

  &__item {
    display: flex;
    margin-left: 57px;
  }
}

.live-dot {
  animation: 1s pulse infinite;
  transition: all ease 0.3s;
  height: 8px;
  width: 8px;
}

@keyframes pulse {
  0% {
    opacity: 0.2;
    box-shadow: 4px 4px 4px red;
  }
  100% {
    height: 8px !important;
    width: 8px !important;
    opacity: 1;
  }
}

@media (max-width: 768px) {
  .day-slot {
    @apply flex flex-col justify-center items-center border-gray-200 border-2 py-2 px-2 my-2 shadow-sm;
    position: relative;
    border-radius: var(--primary-radius);
    height: fit-content !important;

    &.on-air-program {
      @apply py-4;

      .hours-container {
        @apply py-4;
        width: 100%;
        &__divider {
          display: none;
        }
        &__left {
          width: 100%;
        }

        span {
          margin: 0 5px;
        }
        &__item {
          @apply border-l-0 flex flex-row w-full;
          justify-content: center;
          width: 100%;
          padding: 0 0 0 0;
          margin: 0 0 0 0;
        }
      }
    }
  }

  .day-slot__title {
    @apply flex-col;
  }

  .content-body {
    @apply flex-col px-1 items-center;

    .program-name {
      @apply ml-0;
    }
  }

  .hours-container {
    @apply border-l-0 py-4;

    &__divider {
      display: none;
    }
  }

  .program-name {
    text-align:center ;
  }

  .badge-on-air {
    margin: 15px auto;
  }
  .crud-controls {
    display: none;
  }

  .content-body {
    width: 100%;
  }

  .image_cover {
    margin-bottom: 5px;
  }

  .day-slot__description, .day-slot__title {
    margin:  auto;
    padding:  0 0 0 0 !important;
  }

}


</style>
