<template>
    <app-layout :boards="boards">
        <div class="">
            <div class="max-w-8xl mx-auto sm:pr-6 lg:pr-8 flex flex-col md:flex-row">
                <!-- Main board -->
                <div class="w-100 md:w-9/12 md:mx-4 pt-12">
                    <div class="flex justify-between flex-col md:flex-row mx-2 md:mr-2 md:ml-0">
                        <span class="text-3xl font-bold"> Today's Todos </span>

                        <div class="flex items-center">
                            <div class="w-40 mr-2">
                                <multiselect
                                    v-model="selectedStage"
                                    ref="input"
                                    :show-labels="false"
                                    :options="stages"
                                    class="w-full"
                                >
                                <template slot="singleLabel" slot-scope="props">
                                    <span class="option__title">
                                            <i  class="fa fa-briefcase mr-2"></i>
                                            {{ props.option }}
                                        </span>
                                </template>
                                <template slot="option" slot-scope="props">
                                    <div class="option__desc">
                                        <span class="option__title">
                                            <i  class="fa fa-briefcase mr-2"></i>
                                            {{ props.option }}
                                        </span>
                                    </div>
                                </template>
                                </multiselect>
                            </div>
                            <div class="controls h-10 bg-purple-700 rounded-lg">
                                <button
                                    v-for="mode in modes"
                                    :key="mode"
                                    @click="modeSelected=mode"
                                    :class="{'bg-purple-400': mode == modeSelected }"
                                    class="px-8 h-full rounded-lg text-white capitalize">
                                        {{ mode }}
                                </button>
                            </div>

                        </div>
                    </div>

                    <board-item-container
                        v-show="showCommitted"
                        title="Commited"
                        :tasks="committed"
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
                        :tasks="inbox"
                        :tracker="tracker"
                        @update-item="updateItem"
                        @item-clicked="setTaskToTimer"
                    >
                    </board-item-container>
                </div>
                <!-- End of main board -->

                <!-- Right Side -->
                <div class="w-100 md:w-3/12 md:ml-4 pt-12">
                    <span class="text-3xl ml-2 font-bold"> Fast Access </span>

                    <div class="section-card committed mt-5">
                        <header class="bg-blue-400 text-white font-bold flex justify-between">
                            <span>
                                Links
                            </span>
                            <button class="bg-transparent text-white" @click="isLinkFormOpen = !isLinkFormOpen">
                                <i class="fa fa-plus"></i>
                            </button>
                        </header>
                         <div class="body text-gray-600">
                             <link-viewer
                                :links="links"
                                @edit="openLinkForm"

                             ></link-viewer>
                        </div>
                    </div>

                    <div class="section-card committed">
                         <div :class="`bg-${promodoroColor}-400 text-gray-600 font-bold px-0`">
                            <promodoro
                                ref="Promodoro"
                                :tracker.sync="tracker"
                                :timer-color.sync="promodoroColor"
                                :tasks="todo"
                            >
                            </promodoro>
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
                        <p v-for="task in todo" :key="`task-${task.id}`">
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
    import { subDays, toDate } from "date-fns";
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
            todo: {
                type: [Array, Object],
                default() {
                    return []
                }
            },
            scheduled: {
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
            }
        },
        data() {
            return {
                modes: ['inbox', 'committed', 'all'],
                selectedStage: "",
                modeSelected: 'inbox',
                promodoroColor: "red",
                localCommitDate: new Date,
                isLoading: false,
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
                return ['all', 'inbox'].includes(this.modeSelected)
            },
            showCommitted() {
                return ['all', 'committed'].includes(this.modeSelected)
            },
            stages() {
                return uniq(this.todo.map((item) => item.stage));
            },
            inbox() {
                const inbox = this.selectedStage ? this.todo.filter((item) => item.stage == this.selectedStage) : this.todo;
                return orderBy(this.todo,["priority","stage","title"]);
            }
        },
        mounted() {
            if (!this.standup.length) {
                this.isStandupOpen = true;
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
                const yesterday = subDays(new Date(), 1)
                    .toISOString()
                    .slice(0, 10);
                const now = new Date().toISOString().slice(0, 10);
                let completed = this.todo.filter(item => item.done);
                completed = completed.map(item => {
                    item.commit_date = yesterday;
                    return item;
                });

                completed.forEach(async item => {
                    await this.updateItem(item);
                });

                this.updateDayly(now)
                this.isStandupOpen = false;
                this.isLoading = false;
                this.$inertia.reload({ preserveScroll: true })
            },

            getCommitsByDate() {
                const params = `?commit-date=${this.localCommitDate.toISOString().slice(0, 10)}`
                this.$inertia.replace(`/${params}`,
                 {
                    only: ['committed'],
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
                        preserveScroll: true
                    });
                    return true;
                })
            },

            updateDayly(date) {
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
            }
        }
    }
</script>

<style lang="scss">
.section-card {
    @apply bg-white overflow-hidden shadow-xl mx-2 mb-4;
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
