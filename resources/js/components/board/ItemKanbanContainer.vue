<template>
    <div class="flex">
        <div
            v-for="(quadrant, name) in kanbanData"
            :key="name"
            class="w-full border-2 border-white"
            :class="`bg-${quadrant.attributes.color}-300`"
        >
            <div
                class="header capitalize font-bold flex w-full justify-center items-center h-12"
            >
                {{ name }}
            </div>
            <div class="px-2 py-5">
                <p v-for="task in quadrant.childs" :key="`task-${task.id}`">
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
                </p>
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

<style>

</style>
