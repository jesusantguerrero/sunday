<template>
    <div class="flex kanban-container">
        <div
            v-for="(quadrant, name) in kanbanData"
            :key="name"
            class="w-full mx-2"
        >
            <div
                class="header capitalize font-bold flex w-full justify-between items-center h-12"

            >
                <div>
                    <span :class="`text-${quadrant.attributes.color}-400`">
                        {{ name }}
                    </span>
                    <span class="ml-4 text-gray-300"> {{ quadrant.childs.length}} </span>
                </div>
                <el-dropdown trigger="click"  @click.native.prevent>
                    <div class="hover:bg-gray-200 w-5 rounded-full py-2 text-center h-full flex justify-center">
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
            <div class="pr-4 pt-5">
                <div v-for="task in quadrant.childs" :key="`task-${task.id}`" class="task-item">
                    <label class="checkbox-label">
                        <input
                            type="checkbox"
                            @change="$emit('update-item', task)"
                            name=""
                            :id="task.id"
                            v-model="task.done"
                        />
                        <span class="font-bold">
                            <!-- [{{ task.stage.name }}] -->
                        </span>
                        <span>
                            {{ task.title }}
                        </span>
                    </label>
                </div>
                <item-group-cell
                    class="w-full flex items-center"
                    field-name="title"
                    :is-title="true"
                    :index="-1"
                    :item="quadrant.newTask"
                    :is-new="true"
                    @saved="quadrant.newTask['title'] = $event"
                    @keydown.enter="addItem(quadrant)"
                >
                </item-group-cell>
            </div>
        </div>
    </div>
</template>

<script>
import ItemGroupCell from "./ItemGroupCell";

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
        ItemGroupCell
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
    }
}
</script>

<style lang="scss" scoped>
.kanban-container {
    .task-item {
        @apply py-4 px-2 bg-gray-200 my-2;
    }

    .item-container.section-card .body{
        padding-top: 0;
        padding-bottom: 0;
        min-height: unset;
    }

}
</style>
