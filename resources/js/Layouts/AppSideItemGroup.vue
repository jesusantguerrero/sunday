<template>
  <div class="menu-item-group">
    <div
      class="menu-item-group__toggle btn"
      :class="{ active: isActive, current: !isActive && hasActiveChild }"
      @click="emitValue"
    >
      <span class="content side-item">
        <font-awesome-icon :icon="icon" />
        {{ label }}
      </span>

      <span class="indicator">
        <font-awesome-icon :icon="arrowIcon" />
      </span>
    </div>

    <div
      class="menu-item-group__childs"
      :class="{ 'my-collapse': isActive, 'custom-accordion': true }"
    >
      <el-collapse-transition>
        <div class="child-container" v-show="isActive">
          <template v-for="item in childs">
            <app-side-item
              :ref="`${label}-${item.label}`"
              v-if="!item.hide"
              :key="`${label}-${item.label}`"
              :icon="item.icon"
              :label="item.label"
              :to="item.to"
            />
          </template>
        </div>
      </el-collapse-transition>
    </div>
  </div>
</template>

<script>
import AppSideItem from "./AppSideItem";

export default {
  props: {
    icon: {
      type: String,
      required: true
    },
    label: {
      type: String,
      required: true
    },
    childs: {
      type: Array,
      default() {
        return [];
      }
    },
    value: {
      type: String,
      required: true
    },
    trackId: {
      type: String,
      required: true
    }
  },
  components: {
    AppSideItem
  },
  data() {
    return {
      active: false
    };
  },
  computed: {
    arrowIcon() {
      return this.active ? "chevron-down" : "chevron-right";
    },
    isActive() {
      return this.trackId == this.value;
    },
    hasActiveChild() {
      return !this.isActive &&  this.childs.find(item => item.to == this.$route.path)
    }
  },
  methods: {
    emitValue() {
      const current = this.value == this.trackId ? "" : this.trackId;
      this.$emit("input", current);
    },
  }
};
</script>

<style lang="scss" scoped>
.menu-item-group {
  &__toggle {
    text-align: left;
    transition: all ease 0.3s;
    margin: 2px;
    color: #777;
    display: flex;
    justify-content: space-between;
    cursor: pointer;
    overflow: hidden;

    .indicator {
      transition: all ease 0.3s;
    }

    &:hover {
      color: #777 !important;
      background: #eee !important;
    }

    &.active {
      color: #777 !important;
      background: #eee !important;
      overflow-x: visible;

      .indicator {
        transform: rotate(90deg);
      }
    }

    &.current {
      background: dodgerblue;
      color: white;
      box-shadow: transparentize(dodgerblue, $amount: 0.5) 5px 3px 10px;
      overflow-x: visible;

      &:hover {
        color: white !important;
        background: dodgerblue !important;
      }

      .indicator {
        transform: rotate(90deg);
      }
    }
  }
}
</style>

<style lang="scss">
.custom-accordion {
  animation: open 0.3s;
  overflow: hidden;
}

.custom-accordion.my-collapse {
  animation: open 0.3s reverse;
}

@keyframes open {
  0% {
    height: 0;
  }
  50% {
    height: 50%;
  }
  100% {
    max-height: auto;
  }
}
</style>
