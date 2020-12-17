<template>
    <inertia-link
        class="board-item flex py-1 items-center px-5"
        :class="{ active: isActive }"
        :href="board.link"
        :key="board.id"
    >
        <span class="board-item__avatar">
            {{ board.name.slice(0, 1) }}
        </span>

        <span class="w-full block">
            {{ board.name }}
        </span>

        <el-dropdown
            trigger="click"
            @command="$emit('option', board, $event)"
            @click.native.prevent
        >
            <div class="hover:bg-gray-200 w-5 rounded-full py-2 text-center">
                <i class="fa fa-ellipsis-v"></i>
            </div>
            <el-dropdown-menu slot="dropdown">
                <el-dropdown-item command="delete" icon="fa fa-trash"
                    >Delete</el-dropdown-item
                >
            </el-dropdown-menu>
        </el-dropdown>
    </inertia-link>
</template>

<script>
export default {
    props: {
        board: {
            type: Object,
            required: true
        },
        isActive: {
            type: Boolean
        }
    }
};
</script>

<style></style>
