<template>
    <div class="item-container section-card committed my-5">
        <header class="font-bold text-lg text-cool-gray-600">
            {{ title }}
        </header>
        <slot> </slot>
        <div class="body text-gray-600 mb-5">
             <item-group-cell
                v-if="allowAdd"
                class="w-full flex items-center mb-10"
                field-name="title"
                :is-title="true"
                :index="-1"
                :item="newTask"
                :show-controls="true"
                :is-new="true"
                placeholder="Task title"
                @saved="newTask['title'] = $event"
                @keydown.enter="addItem()"
            >
            </item-group-cell>

            <item-container-task
                v-for="task in tasks"
                :key="`task-${task.id}`"
                :task="task"
                :tracker="tracker"
                @item-clicked="$emit('item-clicked', task)"
                @update-item="$emit('update-item', $event)"
            >
            </item-container-task>

            <div
                v-if="!tasks.length"
                class="task-item text-center font-bold text-gray-400"
            >
                There's no items to show
            </div>
        </div>
    </div>
</template>

<script>
import ItemContainerTask from "./itemContainerTask";
import ItemGroupCell from "./ItemGroupCell";

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
            required: false
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
            newTask: {
                title: ""
            }
        }
    },
    async created() {
        if (this.boards.length) {
            this.newTask = {
                board: this.boards[0],
                stage: this.boards[0].stages[0]
            }
            await this.getFieldsData()
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
                this.$set(
                    this.newTask,
                    "fieldsData",
                    data.data
                );
            });
        },
        addItem() {
            const newItem = {...this.newTask}
            const field = newItem.fieldsData.find( field => field.name == 'status' )
            const option = field && field.options.find( option => option.name == 'todo')
            newItem.fields = [
                {
                    field_id: field.id,
                    field_name: field.name,
                    value: option && option.name
                }
            ]
            newItem.board_id = newItem.board.id;
            newItem.stage_id = newItem.stage.id;
            delete newItem.fieldsData;
            this.$emit('update-item', newItem);
            this.$set(this.newTask, "title", "");
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
