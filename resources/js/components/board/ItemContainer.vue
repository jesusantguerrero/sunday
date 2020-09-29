<template>
<div class="section-card committed mt-5">
    <header class="bg-purple-400 text-white font-bold">
        {{ title }}
    </header>
    <slot>

    </slot>
    <div class="body text-gray-600">
        <div></div>
        <p v-for="task in tasks" :key="`task-${task.id}`">
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
        </p>
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

<style>

</style>
