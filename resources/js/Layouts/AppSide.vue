<template>
  <div class="app-side">
      <div class="app-side__section-title">
          <span class="s-logo">S</span><span class="dot">.</span>
      </div>
    <div
      class="nav flex-column nav-pills"
      id="v-pills-tab"
      role="tablist"
      aria-orientation="vertical"
    >
      <template v-for="route in menu">
        <app-side-item
          :icon="route.icon"
          :label="route.label"
          :to="route.to"
          v-if="!route.childs"
          :key="route.label"
        />

        <app-side-item-group
          v-else
          :track-id="route.label"
          :icon="route.icon"
          :label="route.label"
          v-model="activeGroup"
          :childs="route.childs"
          :key="route.label"
        />
      </template>
    </div>
  </div>
</template>

<script>
import AppSideItem from "./AppSideItem";
import AppSideItemGroup from "./AppSideItemGroup";
import AppClock from "./AppClock";

export default {
  props: {
    sideTitle: String,
    menu: {
      type: Array,
      required: true
    }
  },
  components: {
    AppSideItem,
    AppSideItemGroup,
    AppClock
  },
  data() {
    return {
      activeGroup: ""
    };
  }
};
</script>

<style lang="scss" scoped>
.app-side {
  @apply bg-purple-900 text-white;
  height: 100vh;
  display: flex;
  flex-direction: column;
  width: 100%;
  overflow: hidden;
  box-shadow: transparentize($color: #000000, $amount: 0.7) 3px 3px 5px;
  position: relative;
  display: grid;
  grid-template-rows: 64px 1fr 120px;
  z-index: 1001;

  .nav {
    margin-top: 30px !important;
    width: 100%;
    max-height: 100%;
    flex-flow: row;
    padding: 15px;
    overflow-y: auto;

    &::-webkit-scrollbar-thumb {
      background-color: transparentize($color: #000000, $amount: 0.7);
    }

    &::-webkit-scrollbar {
      background-color: transparent;
      width: 8px;
    }
  }

  .nav-link {
    text-align: left;
    transition: all ease 0.3s;
    margin: 2px;

    &:not(.active):hover {
      background: dodgerblue;
      color: white;
    }
  }

  &__section-title {
    @apply font-sans;
    font-style: italic;
    color: white;
    margin: 10px 0;
    padding-left: 15px;
    font-size: 40px;

    .dot {
        @apply text-green-400;
    }
  }

  .router-link {
    &-exact-active {
      background: dodgerblue;
      color: white;
      box-shadow: transparentize(dodgerblue, $amount: 0.5) 5px 3px 10px;
      overflow-x: visible;
    }
  }
}
</style>
