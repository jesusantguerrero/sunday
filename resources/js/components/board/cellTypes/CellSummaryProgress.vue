<template>
<div class="flex py-1"> 
    <div v-for="percentage in values" :class="[percentage.color, 'cursor-pointer hover:scale-105']" :style="getPercentage(percentage.count)" :title="percentage.label">
       
    </div> 
</div>
</template>

<script setup>
import { computed } from "vue";
import { cellProps } from "./mixin";

const emit = defineEmits(['saved', 'closed'])
const props = defineProps({
    ...cellProps,
    users: {
        type: Array,
        default() {
            return []
        }
    }
});

const values = computed(() => {
        return Object.values(props.item[props.fieldName].value)
})

const getPercentage = (count) => {
    return {
        width: `${Math.ceil(count / values.value.length * 100)}%`
    }
}
</script>

<style lang="scss">
.item-group-cell {
    .el-select .el-input.is-focus .el-input__inner {
        border: none;
    }

    .el-input__inner {
        height: 33px;
        border: none;
    }

    .el-date-editor.el-input {
        width: 100%;
        border: none;
    }
}
</style>
