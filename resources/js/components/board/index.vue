<template>
    <div class="px-8 pb-24">
        <div class="board__toolbar flex justify-between">
            <div class="flex text-left">
                <div class="flex justify-between mr-2">
                    <span class="text-3xl font-bold"> {{ board.name }} </span>
                    <div>
                        <span class="automation" v-for="automation in automations" :key="`automation-${automation.id}`">
                            <img :src="automation.service_logo" v-if="automation.service_logo" class="automation-logo">
                            <div v-else>
                                {{ automation.name[0] }}

                            </div>
                        </span>
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
                    class="form-input ml-2 w-48"
                    name=""
                    id=""
                    v-model="searchOptions.search"
                    placeholder="search"
                />
                <span class="ml-2 toolbar-buttons">
                    <i class="fa fa-user"></i>
                </span>
                <span class="ml-2 toolbar-buttons"
                    :class="{active: searchOptions.done}"
                    @click="toggleDone()"
                    ><i class="fa fa-eye"></i
                ></span>
                <span class="ml-2 toolbar-buttons">
                    <i class="fa fa-thumbtack"></i
                ></span>
                <span class="ml-2 toolbar-buttons">
                    <i class="fa fa-filter"></i>
                </span>
                <span class="ml-2 toolbar-buttons">
                    <i class="fa fa-sort"></i>
                </span>
            </div>
        </div>

        <div class="">
            <draggable
                v-model="board.stages"
                @end="saveReorder"
                v-if="modeSelected == 'list'"
                handle=".handle"
            >
                <transition-group>
                    <item-group
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
                    </item-group>
                </transition-group>
            </draggable>

            <item-kanban-container
                v-else
                :stages="board.stages"
                :fields="board.fields"
                :kanban-data="kanbanData"
                @saved="addItem"
                class="flex pt-5">
            </item-kanban-container>

            <div class="w-full flex justify-center py-5" v-if="modeSelected == 'list'">
                <button
                    class="rounded-full flex justify-center items-center px-2 h-8 w-8 border-2 border-purple-400 text-purple-400 hover:bg-purple-400 hover:text-white"
                    @click="addStage()"
                >
                    <i class="fa fa-plus"></i>
                </button>
            </div>
        </div>

        <!-- Delete Team Confirmation Modal -->
        <jet-confirmation-modal
            :show="itemToDelete"
            @close="itemToDelete = false"
        >
            <template #title>
                Delete Team
            </template>

            <template #content>
                Are you sure you want to delete this team? Once a team is
                deleted, all of its resources and data will be permanently
                deleted.
            </template>

            <template #footer>
                <jet-secondary-button @click.native="itemToDelete = false">
                    Nevermind
                </jet-secondary-button>

                <jet-danger-button
                    class="ml-2"
                    @click.native="deleteItem(itemToDelete)"
                >
                    Delete Item
                </jet-danger-button>
            </template>
        </jet-confirmation-modal>

        <item-modal
            @cancel="isItemModalOpen=false"
            @saved="isItemModalOpen=false"
            :record-data="openedItem"
            :is-open="isItemModalOpen">
        </item-modal>
    </div>
</template>

<script>
import JetConfirmationModal from "../../Jetstream/ConfirmationModal";
import JetDangerButton from "../../Jetstream/DangerButton";
import JetSecondaryButton from "../../Jetstream/SecondaryButton";
import ItemGroup from "./ItemGroup.vue";
import ItemModal from "./ItemModal";
import ItemKanbanContainer from "./ItemKanbanContainer.vue";
import Draggable from "vuedraggable";
import { throttle } from "lodash-es";

export default {
    name: "Board",
    components: {
        ItemGroup,
        ItemModal,
        Draggable,
        ItemKanbanContainer,
        JetConfirmationModal,
        JetDangerButton,
        JetSecondaryButton
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
                    icon: "fa fa-th-list"
                },
                kanban:{
                    name: "kanban",
                    title: "Kanban",
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
            isItemModalOpen: false
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

        confirmDeleteItem(item, reload = true) {
            this.itemToDelete = item;
        },

        deleteItem(item, reload = true) {
            axios({
                url: `/items/${item.id}`,
                method: "delete"
            }).then(() => {
                if (reload) {
                    this.itemToDelete = false;
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
                url: `/stages${param}`,
                method,
                data: stage
            }).then(({ data }) => {
                if (reload) {
                    this.$inertia.reload({ preserveScroll: true });
                }
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
        }
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
a {
    color: #42b983;
}

.btn {
    @apply font-bold py-2 px-4 rounded;
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
