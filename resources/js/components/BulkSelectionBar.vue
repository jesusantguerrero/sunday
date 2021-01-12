<template>
    <div  class="bulk-selection-bar">
        <div class="flex items-center">
            {{ selectedItems.length }} Items selected
        </div>
        <div>
            <span class="ml-2 toolbar-buttons" @click="$emit('delete-pressed')">
                <i class="fa fa-trash"></i>
            </span>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        selectedItems: {
            type: Array,
            required: true
        }
    }
}
</script>

<style lang="scss" scoped>
.bulk-selection-bar {
    @apply flex bg-gray-800 text-white px-5 py-3 fixed z-50 bottom-10 right-5;
    width: calc(100% - 22.5rem);
    transition: all ease .3s;
}
</style>
