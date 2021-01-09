<template>
    <el-dropdown
        placement="bottom-start"
        size="mini"
        @command="handleCommands"
    >
        <span :title="value" class="cell-link h-7 w-full">
            <a target="_blank" :href="value" class="flex">
                <img
                    :src="getFavicon(value)"
                    alt=""
                    srcset=""
                    class="favicon"
                />
                <span class="w-full h-full flex align-middle text-left">{{ value }}</span>
            </a>
        </span>
        <el-dropdown-menu slot="dropdown">
            <el-dropdown-item command="edit" icon="fa fa-edit"
                >Edit</el-dropdown-item
            >
            <el-dropdown-item command="copy" icon="fa fa-copy"
                >Copy</el-dropdown-item
            >
            <el-dropdown-item command="go" icon="fa fa-external-link-alt">
                <a :href="value" target="_blank" rel="noopener noreferrer">
                    Go to link
                </a>
            </el-dropdown-item>
        </el-dropdown-menu>
    </el-dropdown>
</template>

<script>
export default {
    props: {
        value: {
            type: String,
            required: true
        }
    },
    methods: {
        getFavicon(url) {
            return url && `https://www.google.com/s2/favicons?domain=${url}`;
        },
        handleCommands(command) {
            switch (command) {
                case "copy":
                    navigator.clipboard.writeText(this.value);
                    this.$notify({
                        type: "success",
                        message: "URL copied"
                    })
                    break;
                case "edit":
                    this.$emit("edit");
                    break;
                default:
                    break;
            }
        }
    }
};
</script>

<style lang="scss" scoped>
.cell-link {
    @apply w-full h-7 text-sm inline-block border-2 border-transparent border-dashed cursor-pointer px-2 overflow-hidden;

    &:hover {
        @apply border-gray-300;
        a {
            @apply text-purple-600;
        }
    }

    a {
        display: flex;
        align-items: center;
        height: 100%;
        overflow: hidden;
        transition: all ease 0.3s;
    }

    .favicon {
        max-height: 20px !important;
        max-width: 20px !important;
        margin-right: 5px;
        border: none;
    }
}
</style>
