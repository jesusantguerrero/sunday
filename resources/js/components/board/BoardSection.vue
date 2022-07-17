<template>
    <div class="px-8 pb-24">
        <div class="flex justify-between board__toolbar">
            <div class="flex text-left">
                <div class="flex justify-between mr-2">
                    <span class="text-3xl font-bold" v-if="!isEditMode">
                        {{ board.name }}
                    </span>
                    <div v-else>
                        <input
                            v-model="board.name"
                            type="text"
                            ref="input"
                            @keypress.enter="updateBoardName(board)"
                        />
                    </div>
                     <i class="mx-2 fa fa-edit" @click="toggleEditMode(true)"></i>
                    <div>
                        <span
                            class="automation"
                            v-for="automation in automations"
                            :key="`automation-${automation.id}`"
                            @click="runAutomation(automation.id)"
                        >
                            <img :src="automation.service_logo" v-if="automation.service_logo" class="automation-logo">
                            <div v-else>
                                {{ automation.name[0] }}

                            </div>
                        </span>
                        <!-- <span class="automation" @click="isAutomationModalOpen=true">
                            <i class="fa fa-plus"></i>
                        </span> -->
                    </div>
                </div>
            </div>

            <div class="flex items-center">
                <div class="w-40">
                    <multiselect
                        v-model="modeSelected"
                        ref="input"
                        :show-labels="false"
                        :options="viewsKeys"
                        class="w-full"
                    >
                     <template slot="singleLabel" slot-scope="props">
                         <span class="option__title">
                                <i :class="views[props.option].icon" class="mr-2"></i>
                                {{ views[props.option].title }}
                            </span>
                    </template>
                    <template slot="option" slot-scope="props">
                        <div class="option__desc">
                            <span class="option__title"><i :class="views[props.option].icon" class="mr-2"></i>
                                {{ views[props.option].title }}
                            </span>
                        </div>
                    </template>
                    </multiselect>
                </div>
                <input
                    type="search"
                    class="w-48 ml-2 form-input"
                    name=""
                    id=""
                    v-model="searchOptions.search"
                    placeholder="search"
                />
                <!-- <span class="ml-2 toolbar-buttons">
                    <i class="fa fa-user"></i>
                </span> -->
                <span class="ml-2 toolbar-buttons"
                    :class="{active: searchOptions.done}"
                    @click="toggleDone()"
                    ><i class="fa fa-eye"></i
                ></span>
                <!-- <span class="ml-2 toolbar-buttons">
                    <i class="fa fa-thumbtack"></i
                ></span> -->
                <!-- <span class="ml-2 toolbar-buttons">
                    <i class="fa fa-filter"></i>
                </span>
                <span class="ml-2 toolbar-buttons">
                    <i class="fa fa-sort"></i>
                </span> -->
            </div>
        </div>

        <bulk-selection-bar
            v-if="selectedItems.length"
            :selected-items="selectedItems"
            @delete-pressed="confirmDeleteItems(selectedItems, true)"
        >
        </bulk-selection-bar>

        <div class="">
            <draggable
                v-model="board.stages"
                v-if="modeSelected == 'list'"
                handle=".handle"
                @end="saveReorder"
            >
                <transition-group>
                    <list-view
                        v-for="stage in board.stages"
                        :key="stage.name"
                        :stage="stage"
                        :board="board"
                        :items="stage.items"
                        :create-mode="createMode"
                        @saved="addItem"
                        @open-item="openItem"
                        @item-deleted="confirmDeleteItem"
                        @stage-updated="addStage"
                        class="mt-10"
                    >
                    </list-view>
                </transition-group>
            </draggable>

            <component
                v-else
                :is="containerComponent"
                :stages="board.stages"
                :fields="board.fields"
                :kanban-data="kanbanData"
                @saved="addItem"
                class="flex pt-5">
            </component>

            <div class="flex justify-center w-full py-5" v-if="modeSelected == 'list'">
                <button
                    class="flex items-center justify-center w-8 h-8 px-2 text-purple-400 border-2 border-purple-400 rounded-full hover:bg-purple-400 hover:text-white"
                    @click="addStage()"
                >
                    <i class="fa fa-plus"></i>
                </button>
            </div>
        </div>

        <item-modal
            @cancel="isItemModalOpen=false"
            @saved="isItemModalOpen=false"
            :record-data="openedItem"
            :is-open="isItemModalOpen">
        </item-modal>

        <automation-modal
            @cancel="isAutomationModalOpen=false"
            @saved="isAutomationModalOpen=false"
            :record-data="{}"
            :board="board"
            :is-open="isAutomationModalOpen">
        </automation-modal>
    </div>
</template>

<script>
import ListView from "./views/List/ItemGroup.vue";
import KanbanView from "./views/kanban/KanbanContainer.vue";
import HabiticaView from "./views/notes/NotesContainer.vue";
import MatrixView from "./views/notes/NotesContainer.vue";
import NoteView from "./views/notes/NotesContainer.vue";
import ItemModal from "./ItemModal.vue";
import AutomationModal from "../AutomationModal.vue";
import BulkSelectionBar from '../BulkSelectionBar.vue';
import Draggable from "vuedraggable";
import { throttle } from "lodash";

export default {
    name: "Board",
    components: {
        ListView,
        KanbanView,
        NoteView,
        HabiticaView,
        MatrixView,
        ItemModal,
        AutomationModal,
        Draggable,
        BulkSelectionBar
    },
    provide() {
       return {
            users: this.users
        };
    },
    props: {
        board: {
            type: Object,
            required: true
        },
        automations: {
            type: Array,
            required: true
        },
        users: {
            type: Array,
            required: true
        },
        filters: {
            type: Object,
            default() {
                return {
                    search: '',
                    done: ''
                }
            }
        }
    },
    data() {
        return {
            createMode: false,
            modeSelected: "list",
            views: {
                list:{
                    name: "list",
                    title: "List",
                    component: "ListView",
                    icon: "fa fa-th-list"
                },
                kanban:{
                    name: "kanban",
                    title: "Kanban",
                    component: "KanbanView",
                    icon: "fa fa-border-all"
                },
                notes:{
                    name: "notes",
                    title: "Notes",
                    component: "NoteView",
                    icon: "fa fa-border-all"
                },
                habitica:{
                    name: "habitica",
                    title: "Habitica",
                    component: "HabiticaView",
                    icon: "fa fa-border-all"
                },
                matrix:{
                    name: "matrix",
                    title: "Matrix",
                    component: "MatrixView",
                    icon: "fa fa-border-all"
                }
            },
            itemToDelete: false,
            items: [
                {
                    title: "Test",
                    owner: "Jesus Guerrero",
                    status: "todo",
                    due_date: new Date().toISOString().slice(0, 10),
                    priority: "High"
                },
                {
                    title: "Test",
                    owner: "Jesus Guerrero",
                    status: "todo",
                    due_date: new Date().toISOString().slice(0, 10),
                    priority: "low"
                },
                {
                    title: "Test",
                    owner: "Jesus Guerrero",
                    status: "todo",
                    due_date: new Date().toISOString().slice(0, 10),
                    priority: "medium"
                }
            ],
            comments: [],
            contacts: [
                {
                    name: "Jesus Guerrero"
                }
            ],
            searchOptions: {
                search: this.filters.search,
                done: this.filters.done
            },
            openedItem: {},
            isEditMode: false,
            isItemModalOpen: false,
            isAutomationModalOpen: false
        };
    },
    watch: {
        searchOptions: {
            handler: throttle(function() {
                let query = this.pickBy(this.searchOptions);
                query = Object.keys(query).length ?  '?' + new URLSearchParams(this.pickBy(this.searchOptions)) : '';
                this.$inertia.replace(`/boards/${this.board.id}${query}`)
            }, 200),
            deep: true,
            immediate: true
        }
    },
    computed: {
        containerComponent() {
            return this.views[this.modeSelected].component
        },

        kanbanData() {
            if (this.board.stages.length) {
                const statusField = this.board.fields.find(
                    field => field.name == "status"
                );
                const quadrants = {};
                this.board.labels.forEach(label => {
                    if (label.field_id == statusField.id) {
                        quadrants[label.name] = {
                            id: label.id,
                            fieldId: label.field_id,
                            attributes: label,
                            childs: [],
                            newTask: {}
                        };
                    }
                });

                this.board.stages.forEach(stage => {
                    stage.items.forEach(item => {
                        const statusField = item.fields.find(
                            field => field.field_name == "status"
                        );
                        if (statusField && quadrants[statusField.value]) {
                            quadrants[statusField.value].childs.push(item);
                        } else {
                            quadrants['backlog'].childs.push(item);
                        }
                    });
                });
                return quadrants;
            }
            return {};
        },

        viewsKeys() {
            return Object.values(this.views).map( view => view.name)
        },

        selectedItems() {
            return this.board.stages.reduce((selectedItems, stage) => {
                selectedItems.push(...stage.items.filter(item => item.selected))
                return selectedItems
            }, [])
        }
    },
    mounted() {
        window.document.onscroll = function() {
            requestAnimationFrame(() => {
                setStickyHeaders()
            })
        };

        function setStickyHeaders() {
            const tables = document.querySelectorAll(".ic-list")
            tables.forEach((table, index) => {
                const falseHeader = table.querySelectorAll('.false-header');

                if (window.pageYOffset >= (table.offsetTop - 60) && window.pageYOffset <= (table.offsetTop + table.offsetHeight - 157)) {
                    table.querySelectorAll('.sticky_header').forEach((header)=> {
                        header.classList.add("sticky-active")
                        falseHeader.forEach((fh) => fh.classList.add('active'))
                        header.style.transform = `translate3d(0px, ${window.pageYOffset - table.offsetTop + 60}px, 1px)`
                    })
                } else {
                    table.querySelectorAll('.sticky_header').forEach((header)=> {
                        header.classList.remove("sticky-active")
                        falseHeader.forEach((fh) => fh.classList.remove('active'))
                        header.style.transform = ``
                    })
                }
            })
        }
    },
    methods: {
        addItem(item, reload = true) {
            const method = item.id ? "PUT" : "POST";
            const param = item.id ? `/${item.id}` : "";
            axios({
                url: `/items${param}`,
                method,
                data: item
            }).then(() => {
                if (reload) {
                    this.$inertia.reload({ preserveScroll: true });
                }
            });
        },

        addStage(stage = {}, reload = true) {
            const method = stage.id ? "PUT" : "POST";
            const param = stage.id ? `/${stage.id}` : "";
            stage.board_id = this.board.id;
            stage.name = stage.name || `Stage ${this.board.stages.length + 1}`;

            return axios({
                url: `/api/stages${param}`,
                method,
                data: stage
            }).then(({ data }) => {
                if (reload) {
                    this.$inertia.reload({ preserveScroll: true });
                }
            });
        },

        updateBoardName(board = {}, reload = true) {
            const method = board.id ? "PUT" : "POST";
            const param = board.id ? `/${board.id}` : "";

            return axios({
                url: `/api/boards${param}`,
                method,
                data: {
                    name: board.name
                }
            }).then(({ data }) => {
                if (reload) {
                    this.$inertia.reload({ preserveScroll: true });
                    this.isEditMode = false;
                }
            });
        },

        addStage(stage = {}, reload = true) {
            const method = stage.id ? "PUT" : "POST";
            const param = stage.id ? `/${stage.id}` : "";
            stage.board_id = this.board.id;
            stage.name = stage.name || `Stage ${this.board.stages.length + 1}`;

            return axios({
                url: `/api/stages${param}`,
                method,
                data: stage
            }).then(({ data }) => {
                if (reload) {
                    this.$inertia.reload({ preserveScroll: true });
                }
            });
        },

        runAutomation(automationId) {
            return axios({
                url: `/api/automations/${automationId}/run`,
                method: "POST"
            }).then(({ data }) => {
                    this.$notify({
                        type: "success",
                        title: "Automation sync",
                        message: "Updated"
                    })
                    this.$inertia.reload({ preserveScroll: true });
            });
        },

        saveReorder() {
            this.board.stages.forEach(async (stage, index) => {
                stage.order = index;
                await this.addStage(stage, false);
            });
            this.$inertia.reload({ preserveScroll: true });
        },

        toggleDone() {
            const nextValues = {
                'with' : 'only',
                'only' : '',
                '' : 'with'
            }

            this.searchOptions.done = nextValues[this.searchOptions.done];
        },

        pickBy(object, predicate) {
            const result = {}
                Object.entries(this.searchOptions).map(([key, value]) => {
                    if (value) {
                        result[key] = value;
                    }
                })

            return result;
        },

        openItem(item) {
            this.isItemModalOpen = true;
            this.openedItem = item;
        },

        confirmDeleteItem(item, reload = true) {
            this.showConfirm({
                title: `Deleting ${item.title} task`,
                content: "Are you sure you want to delete this tasks?",
                confirmationButtonText: "Yes, delete",
                confirm: () => {
                    axios({
                        url: `/items/${item.id}`,
                        method: "delete"
                    }).then(() => {
                        if (reload) {
                            this.itemToDelete = false;
                            this.$inertia.reload({ preserveScroll: true });
                        }
                    });
                }
            });
        },

        confirmDeleteItems(items, reload = true) {
            this.showConfirm({
                title: `Deleting ${items.length} tasks`,
                content: "Are you sure you want to delete these tasks?",
                confirmationButtonText: "Yes, delete",
                confirm: () => {
                    axios({
                        url: `/api/items/bulk/delete`,
                        method: "post",
                        data: items.map(item => item.id)
                    }).then(() => {
                        this.$inertia.reload({ preserveScroll: true });
                    });
                }
            });
        },

        toggleEditMode() {
            this.isEditMode = !this.isEditMode;
            this.$nextTick(() => {
                if (this.$refs.input) {
                    this.$refs.input.focus();
                }
            });
        },
    }
};
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style lang="scss">
h3 {
    margin: 40px 0 0;
}
ul {
    list-style-type: none;
    padding: 0;
}
li.link {
    display: inline-block;
    margin: 0 10px;
}

.board__toolbar {
    border-bottom: 1px solid #ddd;
    padding-bottom: 15px;
}

.toolbar-buttons {
    @apply px-2 rounded-full inline-flex items-center justify-center cursor-pointer;
    width: 34px;
    height: 34px;

    &.active {
        @apply bg-gray-300;
    }

    &:hover {
        @apply bg-gray-300;
    }
}

.form-input {
    @apply shadow appearance-none border rounded py-2 px-3 text-gray-700 leading-tight;
}

.automation-logo {
    width: 20px;
    height: 20px;
    padding: 2px;
    display: inline-block;
    cursor: pointer;
    border-radius: 50%;
    background: white;
    border: 1px solid crimson;
}
</style>
