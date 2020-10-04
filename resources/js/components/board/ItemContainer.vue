<template>
<div class="item-container section-card committed mt-5">
    <header class="bg-purple-400 text-white font-bold">
        {{ title }}
    </header>
    <slot>

    </slot>
    <div class="body text-gray-600">
        <div v-for="task in tasks" :key="`task-${task.id}`" class="task-item">
            <label class="checkbox-label">
            <input
                type="checkbox"
                @change="$emit('update-item', task)"
                name=""
                :id="task.id"
                v-model="task.done"
            />
                <span class="font-bold">
                [{{ task.stage.name }}]
            </span>
            <span>
                {{ task.title }}
            </span>
            </label>
        </div>
        <div v-if="!tasks.length" class="task-item text-center font-bold text-gray-400">
                There's no items to show
        </div>
    </div>
</div>
</template>

<script>
export default {
    props: {
        tasks: {
            type: Array,
            required: true
        },
        title: {
            type: String,
            default: 'Todos'
        }
    }
}
</script>

<style lang="scss" scoped>
.task-item {
    @apply py-4 border-b-2 border-gray-100;
}

.item-container.section-card .body{
    padding-top: 0;
    padding-bottom: 0;
    min-height: unset;
}
</style>
