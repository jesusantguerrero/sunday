<template>
<div class="flex items-center justify-between text-sm font-bold text-ellipsis">
    <WorkspaceIcon :name="option.name" size="sm" class="text-white" />
    <div class="w-full pl-1 font-bold">{{ option.name}} </div>
</div>
</template>


<script>
import WorkspaceIcon from './WorkspaceIcon.vue'

export default {
    components: {
        WorkspaceIcon
    },
    props: {
        option: {
            type: Object,
            required: true
        }
    }
}

</script>
