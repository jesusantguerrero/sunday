<template>
  <div>
    <div class="min-h-screen home-container ic-scroller">
      <nav class="app-header">
        <!-- Primary Navigation Menu -->
        <div class="px-4 mx-auto max-w-8xl sm:px-6 lg:px-8">
          <div class="flex justify-between h-16">
            <div class="flex">
              <!-- Logo -->
              <div class="flex items-center flex-shrink-0">
                <a href="/dashboard"> Daily </a>
              </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ml-6">
              <div class="relative ml-3">
                <jet-dropdown align="right" width="48">
                  <template #trigger>
                    <button
                      class="flex text-sm transition duration-150 ease-in-out border-2 border-transparent rounded-full focus:outline-none focus:border-gray-300"
                    >
                      <img
                        class="w-8 h-8 rounded-full"
                        :src="$page.props.user.profile_photo_url"
                        alt=""
                      />
                    </button>
                  </template>

                  <template #content>
                    <!-- Account Management -->
                    <div class="block px-4 py-2 text-xs text-gray-400">
                      Manage Account
                    </div>

                    <jet-dropdown-link href="/user/profile"> Profile </jet-dropdown-link>

                    <jet-dropdown-link
                      href="/user/api-tokens"
                      v-if="$page.props.jetstream.hasApiFeatures"
                    >
                      API Tokens
                    </jet-dropdown-link>

                    <div class="border-t border-gray-100"></div>

                    <!-- Team Management -->
                    <template v-if="$page.props.jetstream.hasTeamFeatures">
                      <div class="block px-4 py-2 text-xs text-gray-400">Manage Team</div>

                      <!-- Team Settings -->
                      <jet-dropdown-link
                        :href="'/teams/' + $page.props.user.current_team.id"
                      >
                        Team Settings
                      </jet-dropdown-link>

                      <jet-dropdown-link
                        href="/teams/create"
                        v-if="$page.props.jetstream.canCreateTeams"
                      >
                        Create New Team
                      </jet-dropdown-link>

                      <div class="border-t border-gray-100"></div>

                      <!-- Team Switcher -->
                      <div class="block px-4 py-2 text-xs text-gray-400">
                        Switch Teams
                      </div>

                      <template v-for="team in $page.props.user.all_teams" :key="team.id">
                        <form @submit.prevent="switchToTeam(team)">
                          <jet-dropdown-link as="button">
                            <div class="flex items-center">
                              <svg
                                v-if="team.id == $page.props.user.current_team_id"
                                class="w-5 h-5 mr-2 text-green-400"
                                fill="none"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                              >
                                <path
                                  d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                                ></path>
                              </svg>
                              <div>
                                {{ team.name }}
                              </div>
                            </div>
                          </jet-dropdown-link>
                        </form>
                      </template>

                      <div class="border-t border-gray-100"></div>
                    </template>

                    <!-- Authentication -->
                    <form @submit.prevent="logout">
                      <jet-dropdown-link as="button"> Logout </jet-dropdown-link>
                    </form>
                  </template>
                </jet-dropdown>
              </div>
            </div>

            <!-- Hamburger -->
            <div class="flex items-center -mr-2 sm:hidden">
              <button
                @click="showingNavigationDropdown = !showingNavigationDropdown"
                class="inline-flex items-center justify-center p-2 text-gray-400 transition duration-150 ease-in-out rounded-md hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500"
              >
                <svg
                  class="w-6 h-6"
                  stroke="currentColor"
                  fill="none"
                  viewBox="0 0 24 24"
                >
                  <path
                    :class="{
                      hidden: showingNavigationDropdown,
                      'inline-flex': !showingNavigationDropdown,
                    }"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M4 6h16M4 12h16M4 18h16"
                  />
                  <path
                    :class="{
                      hidden: !showingNavigationDropdown,
                      'inline-flex': showingNavigationDropdown,
                    }"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M6 18L18 6M6 6l12 12"
                  />
                </svg>
              </button>
            </div>
          </div>
        </div>

        <!-- Responsive Navigation Menu -->
        <div
          :class="{
            block: showingNavigationDropdown,
            hidden: !showingNavigationDropdown,
          }"
          class="sm:hidden"
        >
          <div class="pt-2 pb-3 space-y-1">
            <jet-responsive-nav-link
              href="/dashboard"
              :active="$page.currentRouteName == 'dashboard'"
            >
              Dashboard
            </jet-responsive-nav-link>
            <jet-responsive-nav-link
              href="/planner"
              :active="$page.currentRouteName == 'planner'"
            >
              Planner
            </jet-responsive-nav-link>
            <jet-responsive-nav-link
              href="/integrations"
              :active="$page.currentRouteName == 'integrations'"
            >
              Integrations
            </jet-responsive-nav-link>
            <jet-responsive-nav-link
              href="/tracker"
              :active="$page.currentRouteName == 'tracker'"
            >
              Time Tracker
            </jet-responsive-nav-link>
          </div>

          <!-- Responsive Settings Options -->
          <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="flex items-center px-4">
              <div class="flex-shrink-0">
                <img
                  class="w-10 h-10 rounded-full"
                  :src="$page.props.user.profile_photo_url"
                  alt=""
                />
              </div>

              <div class="ml-3">
                <div class="text-base font-medium text-gray-800">
                  {{ $page.props.user.name }}
                </div>
                <div class="text-sm font-medium text-gray-500">
                  {{ $page.props.user.email }}
                </div>
              </div>
            </div>

            <div class="mt-3 space-y-1">
              <jet-responsive-nav-link
                href="/user/profile"
                :active="$page.props.currentRouteName == 'profile.show'"
              >
                Profile
              </jet-responsive-nav-link>

              <jet-responsive-nav-link
                href="/user/api-tokens"
                :active="$page.currentRouteName == 'api-tokens.index'"
                v-if="$page.props.jetstream.hasApiFeatures"
              >
                API Tokens
              </jet-responsive-nav-link>

              <!-- Authentication -->
              <form method="POST" @submit.prevent="logout">
                <jet-responsive-nav-link as="button"> Logout </jet-responsive-nav-link>
              </form>

              <!-- Team Management -->
              <template v-if="$page.props.jetstream.hasTeamFeatures">
                <div class="border-t border-gray-200"></div>

                <div class="block px-4 py-2 text-xs text-gray-400">Manage Team</div>

                <!-- Team Settings -->
                <jet-responsive-nav-link
                  :href="'/teams/' + $page.props.user.current_team.id"
                  :active="$page.currentRouteName == 'teams.show'"
                >
                  Team Settings
                </jet-responsive-nav-link>

                <jet-responsive-nav-link
                  href="/teams/create"
                  :active="$page.currentRouteName == 'teams.create'"
                >
                  Create New Team
                </jet-responsive-nav-link>

                <div class="border-t border-gray-200"></div>

                <!-- Team Switcher -->
                <div class="block px-4 py-2 text-xs text-gray-400">Switch Teams</div>

                <template v-for="team in $page.props.user.all_teams">
                  <form @submit.prevent="switchToTeam(team)">
                    <jet-responsive-nav-link as="button">
                      <div class="flex items-center">
                        <svg
                          v-if="team.id == $page.props.user.current_team_id"
                          class="w-5 h-5 mr-2 text-green-400"
                          fill="none"
                          stroke-linecap="round"
                          stroke-linejoin="round"
                          stroke-width="2"
                          stroke="currentColor"
                          viewBox="0 0 24 24"
                        >
                          <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div>{{ team.name }}</div>
                      </div>
                    </jet-responsive-nav-link>
                  </form>
                </template>
              </template>
            </div>
          </div>
        </div>
      </nav>
      <div class="app-content">
        <div class="appside-container">
          <app-side :menu="menu" :header-menu="headerMenu" />
          <!-- Left Side -->
          <board-side :boards="boards" :header-menu="headerMenu" class="h-full mb-10" />
          <!-- End of left side -->
        </div>

        <div class="app-content__inner ic-scroller">
          <!-- Page Content -->
          <main>
            <slot></slot>
          </main>
        </div>
      </div>
    </div>
    <!-- Confirmation Modal -->
    <ConfirmationModal
      :show-modal="showConfirmationModal"
      :modal-data="confirmationData"
      @confirm="handleConfirm"
      @close="handleConfirmClose"
    />
    <!-- Endof confirmation modal -->

    <!-- Modal Portal -->
    <PortalTarget name="modal" multiple />
  </div>
</template>

<script setup>
import Menus from "./menus";
import { computed, onMounted, reactive } from "vue";
import { Inertia } from "@inertiajs/inertia";
import JetDropdown from "./../Jetstream/Dropdown.vue";
import JetDropdownLink from "./../Jetstream/DropdownLink.vue";
import JetResponsiveNavLink from "./../Jetstream/ResponsiveNavLink.vue";
import ConfirmationModal from "../components/shared/ConfirmationModal.vue";
import BoardSide from "../components/board/BoardSide.vue";
import AppSide from "./AppSide.vue";



defineProps({
  boards: {
    type: Array,
    default() {
      return [];
    },
  },
});

const state = reactive({
  showingNavigationDropdown: false,
  showConfirmationModal: false,
  confirmationData: {},
  moduleName: "daily",
  isMenuExpanded: false,
});
const eventBus = {
  $on: () => {},
};
onMounted(() => {
  eventBus.$on("show-modal", (data) => {
    state.showConfirmationModal = true;
    state.confirmationData = data;
  });
});

const closeConfirm = () => {
  state.showConfirmationModal = false;
  state.confirmationData = {};
};

const handleConfirm = () => {
  state.confirmationData.confirm && state.confirmationData.confirm();
  closeConfirm();
};

const handleConfirmClose = () => {
  state.confirmationData.cancel && state.confirmationData.cancel();
  closeConfirm();
};

const switchToTeam = (team) => {
  Inertia.put(
    "/current-team",
    {
      team_id: team.id,
    },
    {
      preserveState: false,
    }
  );
};

const logout = () => {
  axios.post("/logout").then((response) => {
    window.location = "/";
  });
};

const activeModule = computed(() => {
  return Menus(state.moduleName);
});

const menu = computed(() => {
  return activeModule.value.menu;
});

const headerMenu = computed(() => {
  return activeModule.value.headerMenu;
});

const path = computed(() => {
  return window.location.pathname;
});
</script>

<style lang="scss">
.home-container {
  position: relative;
}

.app-header {
  width: 100%;
  top: 0;
  position: fixed;
  background: white;
  border-bottom: 1px solid #f4f5f7;
  z-index: 1000;
}

.appside-container {
  @apply bg-purple-900;
  padding-right: 0 !important;
  position: fixed;
  display: grid;
  grid-template-columns: 66px 1fr;
  width: 300px;
  z-index: 1001;
}

.app-content {
  display: grid;
  grid-template-columns: 300px minmax(0, 1fr);
  background: #f8f8f8 !important;
  position: relative;

  &__inner {
    width: 100%;
    grid-column-start: 2;
    padding: 65px 0;
    padding-bottom: 0;
    position: relative;
    max-height: 100%;
    transition: all ease 0.3s;

    &.header-replacer-mode {
      padding-top: 0;

      .header-replacer {
        height: 73px;
        margin: 0;
        position: fixed;
        left: 0;
        top: 0;
        display: flex;
        width: 100%;
        z-index: 1000;
        background: white;
        align-items: center;
        padding: 0 10px;
      }

      .section-body {
        padding-top: 140px;
      }
    }
  }
}

.splash-screen {
  background: dodgerblue;
  width: 100%;
  height: 100vh;
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
}

.btn {
  @apply font-bold py-2 px-4 rounded;
}

@media screen and (max-width: 992px) {
  .appside-container {
    z-index: 999;
    width: 256px;
    left: -260px;
    transition: all ease 0.3s;
  }

  .app-content__inner {
    grid-column-start: 1;
    grid-column-end: 3;
  }

  .home-container.menu-expanded {
    .appside-container {
      left: 0;
    }
  }
}

@media print {
  .appside-container,
  .no-print,
  button {
    display: none;
  }

  table {
    width: 100% !important;
    overflow: hidden;
  }

  th td {
    overflow: hidden;
  }

  .app-content {
    grid-column-start: 1;
    grid-column-end: 3;
  }
}

.ic-scroller {
  &::-webkit-scrollbar-thumb {
    background-color: transparentize($color: #000000, $amount: 0.7);
    border-radius: 4px;

    &:hover {
      background-color: transparentize($color: #000000, $amount: 0.7);
    }
  }

  &::-webkit-scrollbar {
    background-color: transparent;
    width: 8px;
    height: 10px;
  }

  &-slim {
    transition: all ease 0.3s;
    &::-webkit-scrollbar {
      height: 0;
    }

    &:hover {
      &::-webkit-scrollbar {
        height: 3px;
      }
    }
  }
}
</style>
