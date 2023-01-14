<template>
<div class="flex items-center justify-center mx-auto rounded-md" :style="{backgroundColor: color }" :class="workspaceClass">
    <span class="iconify" data-icon="">
        {{ workspaceIcon }}
    </span>
</div>
</template>

<script>
const SIZES = {
    sm: 'w-6 h-6',
    md: 'w-20 h-20',
    lg: 'w-32 h-32'
}
export default {
    props: {
        name: {
            type: String,
        },
        color: {
            type: String,
            default: '#C084FE'
        },
        size: {
            type: String,
            validator(value) {
               return Object.keys(SIZES).includes(value)
            },
            default: "md"
        }
    },
    computed: {
        workspaceIcon() {
           return this.icon || this.name && this.name[0]
        },
        workspaceClass() {
            return SIZES[this.size]
        }
    }
}

</script>
