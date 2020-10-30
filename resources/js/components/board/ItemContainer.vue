<template>
<div class="item-container section-card committed mt-5">
    <header class="bg-purple-400 text-white font-bold">
        {{ title }}
    </header>
    <slot>

    </slot>
    <div class="body text-gray-600">
        <div v-for="task in tasks" :key="`task-${task.id}`" class="task-item">
            <div>
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
             <button @click="$emit('item-clicked', task)" class="play-button">
                <i class="fa" :class="[isTracker(task) ? 'fa-pause' : 'fa fa-play']"/>
            </button>
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
        },
        tracker: {
            type: Object
        }
    },
    methods: {
        isTracker(task) {
            return this.tracker && this.tracker.timeEntry.item_id == task.id;
        }
    }
}
</script>

<style lang="scss" scoped>
.task-item {
    @apply py-4 border-b-2 border-gray-100 flex justify-between;
}

.item-container.section-card .body{
    padding-top: 0;
    padding-bottom: 0;
    min-height: unset;
}
</style>
