<template>
    <div class="flex notes-container">
        <div
            class="w-full mx-2"
        >
            <div
                class="flex items-center justify-between w-full h-12 font-bold capitalize header"

            >
                <div>
                    <span>
                        Notes
                    </span>
                    <span class="ml-4 text-gray-300"> {{ mails.length}} </span>
                </div>
                <el-dropdown trigger="click"  @click.native.prevent>
                    <div class="flex justify-center w-5 h-full py-2 text-center rounded-full hover:bg-gray-200">
                        <div class="flex items-center mr-2">
                            <i class="fa fa-ellipsis-v"></i>
                        </div>
                    </div>
                    <el-dropdown-menu slot="dropdown">
                        <el-dropdown-item command="edit" icon="fa fa-edit">Edit</el-dropdown-item>
                        <el-dropdown-item command="delete" icon="fa fa-trash">Delete</el-dropdown-item>
                    </el-dropdown-menu>
                </el-dropdown>
            </div>
            <div class="pt-5 pr-4">
                <draggable
                    class="grid  sm:grid-cols-2 md:grid-cols-4 gap-4"
                    :list="kanbanData.backlog.childs"
                    group="tasks"
                    @change="($event) => changeStatus($event, quadrant)"
                >
                    <div v-for="task in mails" :key="`task-${task.id}`" :class="`task-item p-5 bg-white overflow-auto ic-scroller`">
                        <label class="checkbox-label">
                            <span class="font-bold">
                                <!-- [{{ task.stage.name }}] -->
                            </span>

                            <span class="font-bold">
                                {{ task.title }}
                            </span>
                        </label>

                        <div v-html="task.snippet" class="note-body bg-white" />
                    </div>
                </draggable>
                <!-- <item-group-cell
                    class="flex items-center w-full"
                    field-name="title"
                    :is-title="true"
                    :index="-1"
                    :item="kanbanData.backlog.newTask"
                    :is-new="true"
                    @saved="kanbanData.backlog.newTask['title'] = $event"
                    @keydown.enter="addItem(kanbanData.backlog)"
                >
                </item-group-cell> -->
            </div>
        </div>
    </div>
</template>

<script>
import ItemGroupCell from "../../ItemGroupCell.vue";
import { VueDraggableNext as Draggable } from "vue-draggable-next"

export default {
    props: {
        kanbanData: {
            type: Object,
            required: true
        },
        stages: {
            type: Array,
            required: true
        },
        fields: {
            type: Array,
            required: true
        }
    },
    components: {
        ItemGroupCell,
        Draggable
    },
    computed: {
        mails() {
            return this.kanbanData.backlog.childs.filter((item) => item.sender)
        }
    },
    methods: {
        addItem(quadrant) {
            const lastChild = quadrant.childs[quadrant.childs.length - 1]
            const lastItemOrder = Math.max(...quadrant.childs.map(item => item.order))
            const newItem = {...quadrant.newTask}
            newItem.board_id = lastChild ? lastChild.board_id : this.stages[0].board_id
            newItem.stage_id = lastChild ? lastChild.stage_id : this.stages[0].id
            const field = this.fields.find( field => field.id == quadrant.attributes.field_id )
            newItem.fields = [
                {
                    field_id: quadrant.attributes.field_id,
                    field_name: field.name,
                    value: quadrant.attributes.name
                }
            ]
            newItem.order = lastItemOrder + 1;
            this.$emit("saved", {...newItem}, true);
            quadrant.newTask = {};
        },
        changeStatus(event, quadrant) {
            if (event.added) {
                let field = event.added.element.fields.find(field => field.field_id == quadrant.attributes.field_id)
                if (!field) {
                     field = this.fields.find( field => field.id == quadrant.attributes.field_id);
                     event.added.element.fields.push({
                         field_id: field.id,
                         field_name: field.name,
                         value: quadrant.attributes.name
                     })
                } else {
                    field['value'] = quadrant.attributes.name
                }

                this.$emit('saved', event.added.element)
            }
        }
    }
}
</script>

<style lang="scss">
.notes-container {
    .task-item {
        @apply py-4 px-4 my-2;
        min-height: 250px;
        cursor: pointer;
    }

    .note-body {
        margin-top: 15px;
        overflow-wrap: anywhere;
    }

    .item-container.section-card .body{
        padding-top: 0;
        padding-bottom: 0;
        min-height: unset;
    }
}

.sortable-ghost {
    background: white !important;
    opacity: 1;
    transition: all ease .3s;
    transform: rotate(45deg);
}

[draggable=true] {
    border: 1px solid crimson !important;
}
</style>

<style lang="scss" scoped>
.task-item {
    @apply rounded-md border-gray-200 shadow-md;
    transition: all ease .3s;
    border-width: 1px;

    &:hover {
        @apply shadow-lg border-gray-400;

    }
}
</style>
