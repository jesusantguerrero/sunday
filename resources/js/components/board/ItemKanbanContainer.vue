<template>
    <div class="flex">
        <div
            v-for="(quadrant, name) in kanbanData"
            :key="name"
            class="w-full border-2 border-gray-200 mx-2"
        >
            <div
                class="header capitalize font-bold flex w-full justify-center items-center h-12"
                :class="`text-${quadrant.attributes.color}-400`"
            >
                {{ name }}
            </div>
            <div class="px-2 pt-5">
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
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        kanbanData: {
            type: Object,
            required: true
        }
    }
}
</script>

<style lang="scss">
.task-item {
    @apply py-4;
    border-top: 1px solid rgba(0,0,0,.1);
}

.item-container.section-card .body{
    padding-top: 0;
    padding-bottom: 0;
    min-height: unset;
}
</style>
