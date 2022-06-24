<template>
    <app-layout :boards="boards">
        <div class="">
            <div class="flex flex-col mx-auto max-w-8xl sm:pr-6 lg:pr-8 md:flex-row">
                <!-- Main board -->
                <div class="pt-12 w-100 md:w-7/12 lg:w-8/12 md:mx-4">
                    <div class="flex flex-col justify-between mx-2 md:flex-row md:mr-2 md:ml-0">
                        <span class="text-3xl font-bold"> Today's Todos </span>

                        <div class="flex items-center">
                            <div class="w-40 mr-2">
                                <multiselect
                                    v-model="selectedStage"
                                    ref="input"
                                    :show-labels="false"
                                    placeholder="Filter by stage"
                                    :options="stages"
                                    class="w-full"
                                >
                                <template slot="singleLabel" slot-scope="props">
                                    <span class="option__title">
                                            <i  class="mr-2 fa fa-briefcase"></i>
                                            {{ props.option }}
                                        </span>
                                </template>
                                <template slot="option" slot-scope="props">
                                    <div class="option__desc">
                                        <span class="option__title">
                                            <i  class="mr-2 fa fa-briefcase"></i>
                                            {{ props.option }}
                                        </span>
                                    </div>
                                </template>
                                </multiselect>
                            </div>
                            <div class="h-10 bg-purple-700 rounded-lg controls">
                                <button
                                    v-for="mode in modes"
                                    :key="mode"
                                    @click="modeSelected=mode"
                                    :class="{'bg-purple-400': mode == modeSelected }"
                                    class="h-full px-8 text-white capitalize rounded-lg">
                                        {{ mode }}
                                </button>
                            </div>

                        </div>
                    </div>

                    <board-item-container
                        v-show="showCommitted"
                        title="Commited"
                        :tasks="committed"
                        @item-deleted="deleteLocalItem($event, 'committed')"
                        @update-item="updateItem"
                    >
                     <template>
                        <schedule-controls
                            v-if="localCommitDate"
                            v-model="localCommitDate"
                        >

                        </schedule-controls>
                     </template>
                    </board-item-container>

                    <board-item-container
                        v-show="showTodo"
                        title="To Do"
                        :allow-add="true"
                        :boards="boards"
                        :tasks="inbox"
                        :tracker="tracker"
                        @update-item="updateItem"
                        @item-deleted="deleteLocalItem($event, 'todo')"
                        @item-clicked="setTaskToTimer"
                    >
                        <template #empty v-if="committed.length">
                            <div class="w-full mx-auto prose prose-xl text-center">
                                <img src="../../img/undraw_a_day_at_the_park.svg" class="w-4/12 mx-auto">
                                <p class="mt-4"> All tasks done. take a rest</p>
                            </div>
                        </template>

                        <template #empty v-else>
                            <div class="w-full mx-auto prose prose-xl text-center">
                                <img src="../../img/undraw_empty.svg" class="w-4/12 mx-auto">
                                <small class="mt-4 text-gray-400"> Nothing to do. Add new tasks from here or mark in your <a href="#" @click="openBoards">boards</a> as todo</small>
                            </div>
                        </template>
                    </board-item-container>
                </div>
                <!-- End of main board -->

                <!-- Right Side -->
                <div class="pt-12 w-100 md:w-5/12 lg:w-4/12 md:ml-4">
                    <span class="ml-2 text-3xl font-bold"> Tools </span>

                      <div class="mt-5 section-card committed">
                         <div :class="`bg-${promodoroColor} text-gray-600 font-bold px-0`">
                            <promodoro
                                ref="Promodoro"
                                :settings="settings"
                                :tracker.sync="tracker"
                                :timer-color.sync="promodoroColor"
                                :tasks="todo"
                                @stopped="getTodos"
                            >
                            </promodoro>
                        </div>
                    </div>

                    <div class="section-card committed">
                        <header class="flex justify-between font-bold text-white bg-blue-400">
                            <span>
                                Links
                            </span>
                            <button class="text-white bg-transparent" @click="isLinkFormOpen = !isLinkFormOpen">
                                <i class="fa fa-plus"></i>
                            </button>
                        </header>
                         <div class="text-gray-600 bg-blue-400 body">
                             <link-viewer
                                :links="links"
                                @edit="openLinkForm"

                             ></link-viewer>
                        </div>
                    </div>

                    <div class="section-card committed">
                        <header class="flex justify-between font-bold text-white bg-blue-400">
                            <span>
                                Agenda
                            </span>
                            <div>
                                <i class="inline-block ml-4 cursor-pointer fa fa-robot" @click="runAgendaAutomations"></i>
                                <inertia-link class="text-white bg-transparent" href="/planner">
                                    Go to Planner
                                </inertia-link>
                            </div>
                        </header>
                         <div class="text-gray-600 bg-blue-400 body">
                            <div
                                class="p-2 text-white rounded-md cursor-pointer hover:bg-blue-500"
                                v-for="event in agenda"
                                :key="`event-${event.id}`">
                                    <span class="mr-2 font-bold">
                                        {{event.time }}
                                    </span>
                                    <span class="capitalize">
                                        {{ event.title }}
                                    </span>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End of Right Side -->
            </div>

            <dialog-modal :show="isStandupOpen" @close="isStandupOpen=false">
                <template #title>
                    Today's Standup
                </template>

                <template #content>
                    <div>
                        <p v-for="task in standupSummary" :key="`task-${task.id}`">
                            <label class="checkbox-label">
                            <input
                                type="checkbox"
                                @change="updateItem(task)"
                                name=""
                                :id="task.id"
                                v-model="task.done"
                            />
                            <span class="font-bold">
                                [{{ task.stage }}]
                            </span>
                            <span>
                                {{ task.title }}
                            </span>
                            </label>
                        </p>
                    </div>
                </template>

                <template #footer>
                    <primary-button @click.native="completeDay()">
                        Complete Day
                    </primary-button>
                </template>
            </dialog-modal>

            <dialog-modal :show="showWelcomeScreen" @close="hideWelcome">
                <template #content>
                    <div class="prose prose-xl text-center">
                        <div>
                            <img src="../../img/undraw_game_day_ucx9.svg" alt="" class="w-6/12 mx-auto">
                        </div>
                        <h2 class="text-center"> Welcome to Daily.</h2>
                        <div class="mb-5">
                            Create tasks, track the time it takes to get it done, link your promodoros and be more productive!
                        </div>
                        <div class="text-center">
                            <primary-button @click.native="hideWelcome()">
                                Let's start!
                            </primary-button>
                        </div>
                    </div>
                </template>
            </dialog-modal>

            <link-form-modal
                :record-data="linkData"
                :is-open="isLinkFormOpen"
                @saved="onLinkSaved"
                @cancel="isLinkFormOpen=false">
            </link-form-modal>
        </div>
    </app-layout>
</template>

<script>
    import AppLayout from './../Layouts/AppLayout'
    import BoardSide from "../components/board/BoardSide"
    import BoardItemContainer from "../components/board/ItemContainer"
    import ScheduleControls from "../components/schedule/controls";
    import ScheduleView from "../components/schedule"
    import Promodoro from "../components/promodoro/index"
    import DialogModal from "../Jetstream/DialogModal"
    import LinkFormModal from "../components/links/Form"
    import LinkViewer from "../components/links/Viewer"
    import PrimaryButton from "../Jetstream/Button"
    import { subDays, toDate, format } from "date-fns";
    import { uniq, orderBy } from "lodash-es";

    export default {
        components: {
            AppLayout,
            BoardSide,
            BoardItemContainer,
            ScheduleView,
            Promodoro,
            DialogModal,
            LinkFormModal,
            LinkViewer,
            PrimaryButton,
            ScheduleControls
        },
        props: {
            boards: {
                type: Array,
                default() {
                    return []
                }
            },
            boardTypes: {
                type: Array,
                default() {
                    return []
                }
            },
            boardTemplates: {
                type: Array,
                default() {
                    return []
                }
            },
            todo: {
                type: [Array, Object],
                default() {
                    return []
                }
            },
            agenda: {
                type: [Array, Object],
                default() {
                    return []
                }
            },
            commitDate: {
                type: String,
                required: true
            },
            standup: {
                type: Array,
                default() {
                    return []
                }
            },
            links: {
                type: Array,
                default() {
                    return []
                }
            },
            committed: {
                type: [Array, Object],
                default() {
                    return []
                }
            },
            settings: {
                type: Object,
                default() {
                    return {}
                }
            },
            user: {
                type: Object,
                required: true
            }
        },
        data() {
            return {
                modes: ['inbox', 'daily'],
                selectedStage: "",
                modeSelected: 'inbox',
                promodoroColor: "red",
                standupSummary: [],
                localCommitDate: new Date,
                isLoading: false,
                showWelcomeScreen: false,
                isStandupOpen: false,
                isLinkFormOpen: false,
                linkData: {},
                tracker: null
            }
        },
        watch: {
            localCommitDate(newDate, oldDate) {
                if (oldDate &&  (newDate.toISOString().slice(0, 10) != oldDate.toISOString().slice(0, 10))) {
                    this.getCommitsByDate();
                }
            },
        },
        computed: {
            hasCommited() {
                return this.todo.filter(item => item.done).length;
            },
            showTodo() {
                return ['daily', 'inbox'].includes(this.modeSelected)
            },
            showCommitted() {
                return ['daily', 'committed'].includes(this.modeSelected)
            },
            stages() {
                return uniq(this.todo.map((item) => item.stage));
            },
            inbox() {
                const inbox = this.selectedStage ? this.todo.filter((item) => item.stage == this.selectedStage) : this.todo.filter(task => task);
                return orderBy(inbox,["priority", "stage", "title"]);
            }
        },
        mounted() {
            console.log(this.$inertia.form);
            if (!this.standup.length && this.todo.length) {
                this.standupSummary = {...this.todo};
                this.isStandupOpen = true;
            }
            this.showWelcomeScreen = !Boolean(Number(this.settings.hide_welcome_screen));
            if (this.showWelcomeScreen) {
                this.fireworks();
            }
        },
        created() {
            this.setCommitDate()
        },
        methods: {
            setCommitDate() {
                let date = new Date();
                if (this.commitDate) {
                    date = this.commitDate.split("-");
                    date = toDate(new Date(date[0], date[1] - 1, date[2]));
                }
                this.localCommitDate = date;
            },

            completeDay() {
                this.isLoading = true;
                const yesterday = format(subDays(new Date(), 1),"yyyy-MM-dd");
                const now = format(new Date(), "yyyy-MM-dd");
                let completed = this.todo.filter(item => item.done);
                completed = completed.map(item => {
                    item.commit_date = yesterday;
                    return item;
                });

                completed.forEach(async item => {
                    await this.updateItem(item);
                });

                this.updateDaily(now)
                this.isStandupOpen = false;
                this.isLoading = false;
                this.$inertia.reload({ preserveScroll: true, preserveState: true })
            },

            getCommitsByDate() {
                const params = `?commit-date=${this.localCommitDate.toISOString().slice(0, 10)}`
                this.$inertia.replace(`/${params}`,
                 {
                    only: ['committed'],
                    preserveScroll: true,
                    preserveState: true
                 })
            },

            updateItem(item) {
                const method = item.id ? 'PUT' : 'POST';
                const param = item.id ? `/${item.id}`: ''
                axios({
                    url: `/items${param}`,
                    method,
                    data: item
                }).then(() => {
                    this.$inertia.reload({
                        preserveScroll: true,
                        preserveState: true
                    });
                    const deletedIndex = this.todo.findIndex(task => item.id == task.id);
                    this.todo.splice(deletedIndex, 1);

                    if (item.done) {
                        if (!this.todo.length) {
                            this.fireworks();
                        } else {
                            this.celebrate();
                        }
                    }
                    return true;
                })
            },

            updateDaily(date) {
                axios({
                    url: 'standups',
                    method: 'post',
                    data: {
                        date
                    }
                })
            },

            closeLinkForm() {
                this.linkData = {};
                this.isLinkFormOpen = false
            },

            openLinkForm(formData) {
                this.linkData = formData;
                this.isLinkFormOpen = true
            },

            onLinkSaved() {
                this.closeLinkForm();
                this.$inertia.reload({
                    preserveScroll: true
                })
            },

            setTaskToTimer(task) {
                this.$refs.Promodoro.setTask(task);
            },

            getTodos() {
                this.tracker = null;
                axios("/api/items/todos").then(({ data }) => {
                    this.todo = data
                })
            },

            deleteLocalItem(item, listName) {
                const taskIndex = this[listName].findIndex( task => task.id == item.id);
                this[listName].splice(taskIndex, 1);
            },

            runAgendaAutomations() {
                return axios({
                    url: `/api/automations/createTaskFromCalendar/run`,
                    method: "POST"
                }).then(({ data }) => {
                    data.forEach((automation) => {
                        Echo.private(`automations.${automation.id}`)
                            .listen('AutomationCompleted', (e) => {
                                console.log(e);
                                this.$notify({
                                    type: "success",
                                    title: "Automation Completed",
                                    message: "Updated"
                                })
                                this.$inertia.reload({ preserveScroll: true });
                            });
                    })
                });
            },

            hideWelcome(){
                axios({
                    url: "/api/settings",
                    method: "POST",
                    data: {
                        hide_welcome_screen: true
                    }
                }).then(({ data: settings }) => {
                    this.showWelcomeScreen = false;
                    this.$inertia.reload({ preserveScroll: true });
                })
            },

            openBoards() {

            }
        }
    }
</script>

<style lang="scss">
.bg-red {
    @apply bg-red-400;
}

.bg-blue {
    @apply bg-blue-400;
}

.bg-yellow {
    @apply bg-yellow-400;

}

.bg-green {
    @apply bg-green-400;
}

.section-card {
    @apply bg-white overflow-hidden shadow-xl mx-2 mb-4 rounded-md;
    &.margin-0 {
        @apply m-0;
    }

    header {
        @apply p-4;
    }

    .body {
        @apply p-4;
        min-height: 5rem;
    }

    .body.p-0 {
        @apply p-0;
    }
}

button {
    &:focus {
        outline: 0 !important;
    }
}
</style>
