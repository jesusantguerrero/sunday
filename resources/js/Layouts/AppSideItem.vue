<template>
    <el-tooltip class="item" effect="dark" :content="label" placement="right">
        <inertia-link
            class="side-item flex px-2 py-1 items-center px-5"
            :class="{ active: isPath(to) }"
            :href="to"
            :title="label"
        >
            <i :class="`fa fa-${icon}`" class="ic-icon" />
        </inertia-link>
    </el-tooltip>
</template>

<script>
export default {
    props: {
        to: {
            type: String,
            required: true
        },
        icon: {
            type: String,
            required: true
        },
        label: {
            type: String,
            required: true
        },
        classes: {
            type: String,
            default: "nav-link"
        }
    },
    computed: {
        path() {
            return window.location.pathname;
        }
    },
    methods: {
        isPath(url) {
            const link = url.replace(window.location.origin, "");
            console.log(link, window.location.pathname);
            return link == window.location.pathname;
        },
    }
};
</script>

<style lang="scss">
.side-item {
    display: flex;
    justify-content: center;
    align-items: center;
    width: 40px;
    min-width: 40px;
    font-size: 22px;
    height: 40px;
    border-radius: 8px;
    font-weight: bolder;
    margin: 10px auto;
    color: white;
    transition: all ease .3s;

    &:hover, &.active {
        background-color: rgb(172, 148, 250);
    }
}
</style>
