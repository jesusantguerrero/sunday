<template>
  <nav class="app-header navbar navbar-expand-lg navbar-light bg-light">
    <div class="app-side--logo d-none-xs">
      <div class="app-title__container">
        <slot name="logo"> </slot>
        <h5 class="app-title" v-if="!activeModule">{{ sideTitle }}</h5>
        <h5
          class="app-title"
          v-else
          v-html="activeModule.html || activeModule.label"
        >
          {{ sideTitle }}
        </h5>
      </div>
    </div>

    <button
      class="navbar-toggler"
      type="button"
      data-toggle="collapse"
      data-target="#navbarSupportedContent"
      aria-controls="navbarSupportedContent"
      aria-expanded="false"
      aria-label="Toggle navigation"
      @click="toggleMenu"
    >
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="navbar-no-collapse header-content">
      <ul class="navbar-nav ml-auto">
        <li class="nav-item" v-for="item in headerMenu" :key="item.name">
          <dropdown-item label="" :to="item.to" :icon="item.icon" />
        </li>
        <li class="nav-item dropdown">
          <a
            class="nav-link dropdown-toggle"
            href="#"
            id="navbarDropdown"
            role="button"
            data-toggle="dropdown"
            aria-haspopup="true"
            aria-expanded="false"
          >
            <span>
              {{ user.username }}
            </span>
            <vs-avatar
              src="https://avatars2.githubusercontent.com/u/31676496?s=460&v=4"
            />
          </a>
          <div class="dropdown-menu" aria-labelledby="navbarDropdown">
            <dropdown-item
              classes="dropdown-item"
              label="Settings"
              to="/settings"
              icon="cogs"
            />
            <dropdown-item
              classes="dropdown-item"
              label="Profile"
              to="/profile"
              icon="user"
            />
            <div class="dropdown-divider"></div>
            <dropdown-item
              classes="dropdown-item"
              label="Logout"
              to="/logout"
              icon="user"
            />
          </div>
        </li>
      </ul>
    </div>
  </nav>
</template>

<script>
import AppSideItem from "./AppSideItem";

export default {
  components: {
    "dropdown-item": AppSideItem,
  },
  props: {
    sideTitle: String,
    user: {
        type: Object,
        default() {
            return {}
        }
    },
    activeModule: {
      type: [Object, null]
    },
    headerMenu: {
      type: Array,
      default() {
        return [];
      }
    }
  },
  methods: {
    toggleMenu() {
      this.$store.dispatch("toggleMenu");
    }
  }
};
</script>

<style lang="scss" scoped>
.app-header {
  background: white !important;
  margin-bottom: 20px;
  position: fixed;
  width: 100%;
  overflow: hidden;
  height: 70px;
  box-shadow: transparentize($color: #000000, $amount: 0.7) 3px 3px 5px;
  border-bottom: 2px solid var(--primary-color);
  overflow: visible;
  padding: 0 0 0 0;
  z-index: 1000;

  .app-side--logo {
    height: 75px;
    display: flex;
    align-items: center;
    padding: 15px;
    color: var(--primary-color);
    width: 256px;
    height: 100%;
    font-weight: bolder;

    img {
      height: 40px;
    }

    .app-title__container {
      display: flex;
      width: 100%;
      // justify-content: space-around;
      align-items: center;
    }

    .app-title {
      display: inline-block;
      margin: 0;
      margin-left: 10px;
      font-size: 1.6em;
      font-weight: 600;
    }
  }

  .header-content {
    padding: 15px;
    height: 100%;
    display: flex;
  }

  .nav-link {
    height: 100%;
    display: flex;
    align-items: center;
  }

  .nav-item.dropdown {
    overflow-y: visible !important;
  }

  .dropdown-menu {
    left: -50px;
    top: 64px;
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.175);
    border-radius: 0;
    border: 1px solid rgba(0, 0, 0, 0.15);
    padding-top: 0;
    position: absolute !important;
  }
}

.navbar-no-collapse {
  display: inline-flex;
  -ms-flex-positive: 1;
  -webkit-box-flex: 1;
  flex-grow: 1;
  -ms-flex-align: center;
  -webkit-box-align: center;
  align-items: center;
}

.navbar-nav {
  flex-direction: row !important;
  max-height: 171px;
}

.navbar-toggler {
  border: none;

  &:focus {
    outline: none;
  }

  &:hover {
    font-weight: bolder;
  }
}

.section-title {
  margin-bottom: 0;
  font-weight: bolder;
}

@media (max-width: 768px) {
  .app-header {
    width: 100%;
  }

  .section-title {
    margin-right: 5px;
  }

  .d-none-xs {
    display: none;
  }

  .nav-item {
    margin: 0 5px;
  }
}
</style>

<style lang="scss">
@media (max-width: 768px) {
  .app-header {
    width: 100%;
  }

  .section-title {
    margin-right: 5px;
  }

  .d-none-xs {
    display: none !important;
  }
}
</style>
