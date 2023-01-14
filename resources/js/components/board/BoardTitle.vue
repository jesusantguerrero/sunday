<template>
<div class="flex justify-between mr-2">
    <span class="text-3xl  font-bold border border-transparent rounded-md hover:border-slate-300 px-2 w-full" v-if="!isEditMode" @click="toggleEditMode(true)">
        {{ boardName }}
    </span>
    <input
        v-else
        v-model="boardName"
        id="board-name"
        type="text"
        class="inline-block text-2xl  font-bold w-full border rounded-md px-2 focus:outline-none focus:border-purple-500"
        @blur="checkChanges(true)"
        @keypress.enter="checkChanges(true)"
    />
    <div v-if="automations.length">
        <span
            class="automation"
            v-for="automation in automations"
            :key="`automation-${automation.id}`"
            @click="emit('run-automation', automation.id)"
        >
            <img :src="automation.service_logo" v-if="automation.service_logo" class="automation-logo">
            <div v-else>
                {{ automation.name[0] }}

            </div>
        </span>
        <!-- <span class="automation" @click="isAutomationModalOpen=true">
            <i class="fa fa-plus"></i>
        </span> -->
    </div>
</div>
</template>

<script setup>
import { ref, nextTick } from 'vue';

const props = defineProps({
    board: {
        type: Object,
        required: true
    },
    automations: {
        type: Array,
        default: () => ([])
    }
});

const emit = defineEmits(['saved']);

const isEditMode = ref();
function toggleEditMode() {
    isEditMode.value = !isEditMode.value;
    nextTick(() => {
        document.querySelector('#board-name')?.focus()
    });
};

const boardName = ref(props.board.name)
const checkChanges = (shouldToggle) => {
    if (boardName.value !== props.board.name) {
        const changes = {...props.board, name: boardName.value}
        emit('saved', changes)
    }
    if (shouldToggle) {
        toggleEditMode()
    }
}
</script>
