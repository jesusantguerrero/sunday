<template>
<div class="text-sm checklist-container" ref="checklistContainer">
    <h4 class="font-bold text-gray-500 dark:text-gray-300">
        Checklist ({{ doneItems }} / {{ items ? items.length : 0 }})
    </h4>
   <draggable :model-value="items" @update:modelValue="$emit('update:items')" handle=".handle">
        <div
            v-for="(check, index) in items"
            :key="check.id"
            class="flex items-center justify-between h-8 px-2 my-2 bg-white rounded-sm cursor-default checklist__item dark:bg-gray-700 dark:text-gray-300 hover:bg-gray-50"
        >
            <div class="flex items-center w-full">
                <i class="mr-2 opacity-0 fa fa-arrows-alt checklist-item__move handle"  v-if="allowEdit"></i>
                <input
                    type="checkbox"
                    class="mx-2 form-control-check checkbox-done"
                    v-model="check.done"
                    @change.stop="trackChanges"
                />
                <input
                    type="text"
                    class="w-full ml-2 bg-transparent cursor-default focus:outline-none"
                    :disabled="!allowEdit"
                    v-model="check.title"
                />
            </div>

            <button class="w-5">
                <i
                    class="text-gray-400 opacity-0 cursor-pointer fa fa-trash checklist-item__delete hover:text-red-300"
                    @click="deleteItem(index)"
                    v-if="allowEdit"
                ></i>
            </button>
        </div>
    </draggable>

    <div class="flex items-center justify-between px-2 bg-gray-100 border-2 border-gray-100 rounded-md shadow-sm text dark:bg-gray-800 dark:border-gray-600"
        :class="{'border-gray-400': isFocused}"
        v-if="allowEdit"
    >
        <input type="checkbox" disabled class="mr-2">
        <input
            :value="modelValue"
            class="w-full h-8 bg-gray-100 dark:bg-gray-800 dark:text-gray-300 focus:outline-none"
            type="text"
            ref="input"
            @input="emit('update:modelValue', input.value)"
            @keydown.enter.exact.prevent="saveItem()"
            @focus="isFocused=true"
            @blur="isFocused=false"
            placeholder="+ Add an item and press enter"
        >
    </div>
</div>
</template>

<script setup>
import { onClickOutside } from "@vueuse/core";
import { ref, onMounted, onUnmounted, computed, watch } from "vue";
import { VueDraggableNext as Draggable } from "vue-draggable-next"

const props = defineProps({
    modelValue: String,
    items: Array,
    task: Object,
    allowEdit: Boolean
})

const emit = defineEmits({
    'updated': Array,
    'update:items': Array,
    'update:modelValue': String
})

const isFocused = ref(false)
const input = ref(null)

const checkItemTitle = ref("")
watch(() => props.modelValue, (value) => {
    if (value != checkItemTitle.value) {
        checkItemTitle.value = value;
    }
})

const deleteItem = (index) => {
    props.items.splice(index, 1);
}

const clearForm = () => {
    emit('update:modelValue', "")
}

const saveItem = () => {
    if (!checkItemTitle.value) {
        ElNotification({
            title: "Missing Title",
            message: "Title is required for a checklist item"
        })
        return
    }
    props.items.push({title: checkItemTitle.value, done: false });
    clearForm()
    input.value && input.value.focus();
}

const doneItems = computed(() => {
    return Number(props.items && props.items.filter(item => item.done).length || 0)
})

const checklistContainer = ref(null)
const hasChanges = ref(false)

const trackChanges = () => {
    hasChanges.value = true;
}

const updateTask  = () => { }

const updateItems = () => {
    if (hasChanges.value && props.task && props.task.uid) {
        updateTask({
            uid: props.task.uid,
            checklist: [...props.items]
        })
        hasChanges.value = false
    }
}

watch(() => props.task && props.task.uid, () => {
    clearForm()
})

onMounted(() => {
    onClickOutside(checklistContainer, () => {
        updateItems()
    })
})

onUnmounted(() => {
    updateItems()
})
</script>

<style lang="scss" scoped>
.checklist-item__move {
    @apply rounded-md;
    cursor: grab;

}

.ghost .checklist-item__move {
    cursor: grabbing;

}
.sortable-chosen {
    @apply border-green-400;
    border-width: 1px;
    cursor: grabbing !important;
}

.checklist__item {
    &:hover {
        .checklist-item__move,
        .checklist-item__delete {
            opacity: 1;
        }
    }
}
</style>
