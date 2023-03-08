<template>
    <div class="px-8 pb-24">
        <header class="flex justify-between board__toolbar">
            <BoardTitle
                class="w-full"
                :board="board"
                @saved="updateBoardName"
                :automations="automations"
                @run-automation="runAutomation"
            />

            <div class="flex items-center">
                <div class="w-40">
                    <multiselect
                        v-model="modeSelected"
                        ref="input"
                        v-if="false"
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
                ></span>
                <span class="ml-2 toolbar-buttons">
                    <i class="fa fa-filter"></i>
                </span>
                -->
                <span class="ml-2 toolbar-buttons" :class="{active: searchOptions.sort}" @click="clearSort()">
                    <i class="fa fa-sort"></i>
                </span>
            </div>
        </header>

        <BulkSelectionBar
            v-if="selectedItems.length"
            :selected-items="selectedItems"
            @delete-pressed="confirmDeleteItems(selectedItems, true)"
        />

        <div class="">
            <draggable
                v-model="board.stages"
                v-if="modeSelected == 'list'"
                handle=".handle"
                @end="saveReorder"
            >
                <TransitionGroup>
                    <ListView
                        v-for="stage in board.stages"
                        :key="stage.name"
                        :stage="stage"
                        :board="board"
                        :items="stage.items"
                        :create-mode="createMode"
                        :filters="filters"
                        @sort="sort"
                        @clear-sort="clearSort"
                        @saved="addItem"
                        @open-item="openItem"
                        @item-deleted="confirmDeleteItem"
                        @stage-updated="addStage"
                        class="mt-10"
                    />
                </TransitionGroup>
            </draggable>

            <component
                v-else
                :is="containerComponent"
                :stages="board.stages"
                :fields="board.fields"
                :kanban-data="kanbanData"
                @saved="addItem"
                class="flex pt-5"
            />

            <div class="flex justify-center w-full py-5" v-if="modeSelected == 'list'">
                <button
                    class="flex items-center justify-center w-8 h-8 px-2 text-purple-400 border-2 border-purple-400 rounded-full hover:bg-purple-400 hover:text-white"
                    @click="addStage()"
                >
                    <i class="fa fa-plus"></i>
                </button>
            </div>
        </div>

        <ItemModal
            @cancel="isItemModalOpen=false"
            @saved="isItemModalOpen=false"
            :record-data="openedItem"
            :is-open="isItemModalOpen"
        />

        <AutomationModal
            @cancel="state.isAutomationModalOpen=false"
            @saved="state.isAutomationModalOpen=false"
            :record-data="{}"
            :board="board"
            :is-open="state.isAutomationModalOpen"
        />
    </div>
</template>

<script setup>
import ListView from "./views/List/ListView.vue";
import MatrixView from "./views/matrix/MatrixBoard.vue";
import ItemModal from "./ItemModal.vue";
import AutomationModal from "../AutomationModal.vue";
import BulkSelectionBar from '../BulkSelectionBar.vue';
import { VueDraggableNext as Draggable } from "vue-draggable-next"
import { throttle } from "lodash";
import { Inertia } from "@inertiajs/inertia";
import { onMounted, provide, computed, watch, reactive, ref, toRefs } from "vue";
import BoardTitle from "./BoardTitle.vue";

const props = defineProps({
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
                done: '',
                sort: ''
            }
        }
    }
});

provide('users', props.users);
const views = {
    list:{
        name: "list",
        title: "List",
        component: ListView,
        icon: "fa fa-th-list"
    },
    matrix:{
        name: "matrix",
        title: "Matrix",
        component: MatrixView,
        icon: "fa fa-border-all"
    }
};

const state = reactive({
    createMode: false,
    modeSelected: "list",
    itemToDelete: false,
    items: [],
    comments: [],
    contacts: [
        {
            name: "Jesus Guerrero"
        }
    ],
    searchOptions: {
        search: props.filters.search,
        done: props.filters.done,
        sort: props.filters.sort
    },
    openedItem: {},
    isEditMode: false,
    isItemModalOpen: false,
    isAutomationModalOpen: false
});

watch(state.searchOptions, throttle(() => {
    let query = pickBy(state.searchOptions);
    query = Object.keys(query).length ?  '?' + new URLSearchParams(pickBy(state.searchOptions)) : '';
    Inertia.replace(`/boards/${props.board.id}${query}`)
}, 200),{
    deep: true,
    immediate: true
});

const containerComponent = computed(() => {
    return views[state.modeSelected].component
});

const kanbanData = computed(() => {
    const statusField = props.board.fields.find(
        field => field.name == "status"
    );

    if (props.board.stages.length) {
        const quadrants = {};
        props.board.labels.forEach(label => {
            if (label.field_id == statusField.id) {
                quadrants[label.name] = {
                    id: label.id,
                    fieldId: label.field_id,
                    attributes: label,
                    items: [],
                    newTask: {}
                };
            }
        });

        props.board.stages.forEach(stage => {
            stage.items.forEach(item => {
                if (quadrants[item[statusField.name]]) {
                    quadrants[item[statusField.name]].items.push(item);
                } else {
                    quadrants['backlog'].items.push(item);
                }
            });
        });
        return quadrants;
    }
    return {};
});

const viewsKeys = computed(() => {
    return Object.values(views).map( view => view.name)
});

const selectedItems = computed(() => {
    return props.board.stages.reduce((selectedItems, stage) => {
        selectedItems.push(...stage.items.filter(item => item.selected))
        return selectedItems
    }, [])
});

onMounted(() => {
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
});

const isLoading = ref(false)
function addItem(item, reload = true) {
    const method = item.id ? "PUT" : "POST";
    const param = item.id ? `/${item.id}` : "";
    if (isLoading.value) return
    isLoading.value = true;
    axios({
        url: `/items${param}`,
        method,
        data: item
    }).then(() => {
        if (reload) {
            Inertia.reload({
                preserveScroll: true ,
                preserveState: true
            });
        }
    }).finally(() => {
        isLoading.value = false
    });
}

function addStage(stage = {}, reload = true) {
    const method = stage.id ? "PUT" : "POST";
    const param = stage.id ? `/${stage.id}` : "";
    stage.board_id = props.board.id;
    stage.name = stage.name || `Stage ${props.board.stages.length + 1}`;

    return axios({
        url: `/api/stages${param}`,
        method,
        data: stage
    }).then(({ data }) => {
        if (reload) {
            Inertia.reload({ preserveScroll: true });
        }
    });
}

function updateBoardName(board = {}, reload = true) {
    const method = board.id ? "PUT" : "POST";
    const param = board.id ? `/${board.id}` : "";

    return axios({
        url: `/api/boards${param}`,
        method,
        data: {
            name: board.name
        }
    }).then(() => {
        if (reload) {
            Inertia.reload({ preserveScroll: true });
            props.isEditMode = false;
        }
    });
}


function runAutomation(automationId) {
    return axios({
        url: `/api/automations/${automationId}/run`,
        method: "POST"
    }).then(({ data }) => {
        this.$notify({
            type: "success",
            title: "Automation sync",
            message: "Updated"
        })
        Inertia.reload({ preserveScroll: true });
    });
}

function saveReorder() {
    props.board.stages.forEach(async (stage, index) => {
        stage.order = index;
        await addStage(stage, false);
    });
    Inertia.reload({ preserveScroll: true });
}

function toggleDone() {
    const nextValues = {
        'with' : 'only',
        'only' : '',
        '' : 'with'
    }

    state.searchOptions.done = nextValues[state.searchOptions.done];
}

function pickBy(object) {
    return object ? Object.entries(object).reduce((result, [key, value]) => {
        if (value) {
            result[key] = value;
        }
        return result;
    }, {}) : {}
}

function sort(field) {
    if (state.searchOptions.sort == field) {
        state.searchOptions.sort = `-${field}`;
    } else {
        state.searchOptions.sort = field;
    }
}

function clearSort() {
    state.searchOptions.sort = ""
}

function openItem(item) {
    state.isItemModalOpen = true;
    state.openedItem = item;
}

function confirmDeleteItem(item, reload = true) {
    showConfirm({
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
}

function confirmDeleteItems(items, reload = true) {
    showConfirm({
        title: `Deleting ${items.length} tasks`,
        content: "Are you sure you want to delete these tasks?",
        confirmationButtonText: "Yes, delete",
        confirm: () => {
            axios({
                url: `/api/items/bulk/delete`,
                method: "post",
                data: items.map(item => item.id)
            }).then(() => {
                Inertia.reload({ preserveScroll: true });
            });
        }
    });
}

const {
    createMode,
    modeSelected,
    searchOptions,
    openedItem,
    isEditMode,
    isItemModalOpen
} = toRefs(state)
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
