<template>
  <div class="home-container" :class="{ 'menu-expanded': isMenuExpanded }">
    <app-header :active-module="activeModule" :header-menu="headerMenu" :user="user">
      <template v-slot:logo>
        <small>
          <!-- <img src="@/assets/img/favicon-96x96.png" alt="logo" /> -->
        </small>
      </template>
    </app-header>

    <div class="app-content">
      <div class="appside-container">
        <app-side :menu="menu" :show-clock="true"/>
      </div>

      <div class="app-content__inner">
        <slot> </slot>
      </div>
    </div>
  </div>
</template>

<script>
import AppHeader from "./AppHeader";
import AppSide from "./AppSide";
import Menus from "./menus";

export default {
  name: "app-layout",
  props: {
    user: {
        type: Object,
        default() {

        }
    },
  },
  components: {
    AppSide,
    AppHeader
  },
  computed: {
    activeModule() {
      return Menus(this.moduleName);
    },
    menu() {
      return this.activeModule.menu;
    },
    headerMenu() {
      return this.activeModule.headerMenu;
    }
  },
  data() {
    return {
      moduleName: "accounting",
      isMenuExpanded: false
    };
  }
};
</script>
