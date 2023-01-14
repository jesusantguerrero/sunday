<template>
<div class="inline-flex text-white py-1  justify-center group transition cursor-pointer">
    <div class="rounded-md bg-slate-900 space-x-2 px-4 flex items-center group-hover:hidden">
        <span :class="['cursor-pointer text-small']">
            {{ formatHumanDate(values.min) }}
        </span>
        <span>
            -
        </span> 
        <span :class="['cursor-pointer text-small']">
            {{ formatHumanDate(values.max) }}
        </span> 
    </div>
    <div class="hidden rounded-md items-center bg-slate-900 px-4 group-hover:flex">
        {{ days }}
    </div>
</div>
</template>

<script setup>
import { formatHumanDate } from "@/utils/useDateTime";
import { differenceInCalendarDays } from "date-fns";
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
    return props.item[props.fieldName].value
})

const days = computed(() =>
differenceInCalendarDays(values.value.max, values.value.min ))

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
