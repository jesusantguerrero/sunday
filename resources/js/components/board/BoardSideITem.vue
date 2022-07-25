<template>
    <inertia-link
        class="board-item flex py-1 items-center px-5"
        :class="{ active: isActive }"
        :href="`/boards/${board.id}`"
        :key="board.id"
    >
        <span class="board-item__avatar">
            {{ board.name.slice(0, 1) }}
        </span>

        <span class="w-full block">
            {{ board.name }}
        </span>

        <NDropdown
            trigger="click"
            :options="options"
            @select="$emit('option', board, $event)"
        >
            <button class="hover:bg-gray-200 w-5 rounded-full py-2 text-center" @click.prevent.stop>
                <i class="fa fa-ellipsis-v"></i>
            </button>
        </NDropdown>
    </inertia-link>
</template>

<script setup>
import { NDropdown } from "naive-ui"

defineProps({
        board: {
            type: Object,
            required: true
        },
        isActive: {
            type: Boolean
        }
});

const options = [
    {
        label: 'Delete',
        key:"delete"
    }
]
</script>

<style></style>
