<template>
    <inertia-link
        class="board-item flex py-1 items-center px-5"
        :class="{ active: isActive }"
        :href="section.to"
    >
        <span class="board-item__avatar">
            <i :class="`fa fa-${section.icon}`"></i>
        </span>

        <span class="w-full block">
            {{ section.label }}
        </span>
    </inertia-link>
</template>

<script>
export default {
    props: {
        section: {
            type: Object,
            required: true
        },
        isActive: {
            type: Boolean
        }
    }
};
</script>

<style lang="scss" scoped>
    .board-item {
        height: 40px;
        margin: 10px 0;
    }
</style>
