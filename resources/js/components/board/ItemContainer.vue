<template>
    <div class="item-container section-card committed mt-5">
        <header class="bg-purple-400 text-white font-bold">
            {{ title }}
        </header>
        <slot> </slot>
        <div class="body text-gray-600">
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

export default {
    components: {
        ItemContainerTask
    },
    props: {
        tasks: {
            type: Array,
            required: true
        },
        title: {
            type: String,
            default: "Todos"
        },
        tracker: {
            type: Object
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
