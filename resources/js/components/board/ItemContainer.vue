<template>
    <div class="my-5 item-container section-card committed">
        <header class="text-lg font-bold text-cool-gray-600">
            {{ title }}
        </header>
        <slot> </slot>
        <div class="mb-5 text-gray-600 body">
            <item-group-cell
                ref="ItemGroupCell"
                v-if="allowAdd"
                class="flex items-center w-full mb-10"
                field-name="title"
                :is-title="true"
                :index="-1"
                :item="newTask"
                :show-controls="true"
                :close-on-blur="false"
                :is-new="true"
                :boards="boards"
                placeholder="Task title"
                @saved="newTask['title'] = $event"
                @keydown.enter="addItem()"
            >
            </item-group-cell>
            <div v-if="isLoading" class="w-full text-center text-purple-400">
                Adding new task...
            </div>
            <item-container-task
                v-for="task in tasks"
                :key="`task-${task.id}`"
                :task="task"
                :tracker="tracker"
                @item-clicked="$emit('item-clicked', task)"
                @item-deleted="confirmDeleteItem($event, true)"
                @update-item="$emit('update-item', $event)"
            >
            </item-container-task>

            <div
                v-if="!tasks || !tasks.length"
                class="font-bold text-center text-gray-400 task-item"
            >
                <slot name="empty">
                    There's no items to show
                </slot>
            </div>
        </div>
    </div>
</template>

<script>
import ItemContainerTask from "./ItemContainerTask.vue";
import ItemGroupCell from "./ItemGroupCell.vue";

export default {
    components: {
        ItemContainerTask,
        ItemGroupCell
    },
    props: {
        tasks: {
            type: Array,
            required: true
        },
        boards: {
            type: Array,
            required: false,
            defauult() {
                return []
            }
        },
        allowAdd: {
            type: Boolean,
            default: false
        },
        title: {
            type: String,
            default: "Todos"
        },
        tracker: {
            type: Object
        }
    },
    data() {
        return {
            isLoading: false,
            newTask: {
                title: "",
                board: null,
                stage: null
            }
        };
    },
    async created() {
        if (this.boards && this.boards.length) {
            this.newTask.board = this.boards[0];
            this.newTask.stage = this.boards[0].stages[0];
            await this.getFieldsData();
        }
    },
    methods: {
        getFieldsData() {
            return axios({
                url: "/api/fields",
                params: {
                    "filter[board_id]": this.newTask.board.id
                }
            }).then(({ data }) => {
                this.newTask["fieldsData"] = data.data;
            });
        },

        addItem() {
            const newItem = { ...this.newTask };
            const field = newItem.fieldsData.find(
                field => field.name == "status"
            );
            const option =
                field && field.options.find(option => option.name == "todo");
            newItem.fields = [
                {
                    field_id: field.id,
                    field_name: field.name,
                    value: option && option.name
                }
            ];
            newItem.board_id = newItem.board.id;
            newItem.stage_id = newItem.stage.id;
            delete newItem.fieldsData;
            this.updateItem(newItem);
            this.newTask.title = "";
        },

        updateItem(item) {
            const method = item.id ? "PUT" : "POST";
            const param = item.id ? `/${item.id}` : "";
            this.isLoading = true;
            axios({
                url: `/items${param}`,
                method,
                data: item
            }).then(() => {
                this.$inertia.reload({
                    preserveScroll: true
                });
            }).finally(() => {
                this.isLoading = false
            });
        },

        confirmDeleteItem(item, reload = true) {
            this.showConfirm({
                title: `Delete ${item.title} task`,
                content: "Are you sure you want to delete this task?",
                confirmationButtonText: "Yes, delete",
                confirm: () => {
                    axios({
                        url: `/items/${item.id}`,
                        method: "delete"
                    }).then(() => {
                        this.$emit("item-deleted", item);
                    });
                }
            });
        }
    }
};
</script>

<style lang="scss" scoped>
.task-item {
    @apply py-4 border-b-2 border-gray-100 flex justify-between;
}

.item-container.section-card .body {
    padding-top: 0;
    padding-bottom: 0;
    min-height: unset;
}
</style>
