<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Dashboard
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-8xl mx-auto sm:px-6 lg:px-8 flex">
                <div class="w-2/12 mr-4">
                    <board-side
                        :boards="boards"
                        class="mb-10"
                    >
                    </board-side>
                    <div class="section-card committed margin-0 mt-10">
                        <header class="bg-green-500 text-white font-bold">
                            Events
                        </header>
                        <div class="body text-gray-600">
                            Hola soy un item de ejemplo
                        </div>
                    </div>
                </div>

                <div class="w-7/12 mx-2">
                    <span class="text-3xl font-bold"> Today's Todos </span>
                    <div class="section-card committed mt-10">
                        <header class="bg-purple-400 text-white font-bold">
                            To Do
                        </header>
                        <div class="body text-gray-600">
                            <p v-for="task in todo" :key="`task-${task.id}`">
                                <label class="checkbox-label">
                                <input
                                    type="checkbox"
                                    @change="updateItem(task)"
                                    name=""
                                    :id="task.id"
                                    v-model="task.done"
                                />
                                <span>
                                    {{ task.title }}
                                </span>
                                </label>
                            </p>

                            <button @click="completeDay()" v-if="hasCommited">Complete Day</button>
                        </div>
                    </div>
                </div>

                <div class="w-3/12 ml-4">
                    <span class="text-3xl ml-2"> Fast Acess </span>
                    <div class="section-card committed mt-10">
                        <header class="bg-yellow-500 text-white">
                            Comitted
                        </header>
                        <div class="body bg-yellow-200 text-gray-600">
                           <p v-for="task in committed" :key="`task-${task.id}`">
                                <label class="checkbox-label">
                                <input
                                    type="checkbox"
                                    name=""
                                    disabled
                                    :id="task.id"
                                    v-model="task.done"
                                />
                                <span>
                                    {{ task.title }}
                                </span>
                                </label>
                            </p>
                        </div>
                    </div>

                    <div class="section-card committed">
                        <header class="bg-blue-400 text-white font-bold">
                            Links
                        </header>
                         <div class="body text-gray-600">
                            Hola soy un item de ejemplo
                        </div>
                    </div>

                    <div class="section-card committed">
                         <div class="body bg-red-400 text-gray-600 font-bold px-0">
                            <promodoro></promodoro>
                        </div>
                    </div>
                </div>
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
        </div>
    </app-layout>
</template>

<script>
    import AppLayout from './../Layouts/AppLayout'
    import BoardSide from "../components/board/BoardSide"
    import BoardTaskForm from "../components/board/TaskForm"
    import Promodoro from "../components/promodoro/index"
    import DialogModal from "../Jetstream/DialogModal"
    import PrimaryButton from "../Jetstream/Button"
    import { subDays } from "date-fns";

    export default {
        components: {
            AppLayout,
            BoardSide,
            BoardTaskForm,
            Promodoro,
            DialogModal,
            PrimaryButton
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
            standup: {
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
                isLoading: false,
                isStandupOpen: false
            }
        },
        computed: {
            hasCommited() {
                return this.todo.filter(item => item.done).length;
            }
        },
        mounted() {
            if (!this.standup.length) {
                this.isStandupOpen = true;
            }
        },
        methods: {
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

            updateItem(item) {
                const method = item.id ? 'PUT' : 'POST';
                const param = item.id ? `/${item.id}`: ''
                axios({
                    url: `/items${param}`,
                    method,
                    data: item
                }).then(() => {
                    return true
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
}

button {
    &:focus {
        outline: 0 !important;
    }
}
</style>
