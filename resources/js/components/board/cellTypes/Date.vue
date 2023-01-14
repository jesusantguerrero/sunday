<template>
<DatePicker
    v-model:value="localValue"
    ref="input"
    type="date"
    input-class="w-full"
    placeholder="selecciona una fecha"
    @blur="$emit('closed')"
/>
</template>

<script setup>
import { cellProps, formatValue } from "./mixin";
import { computed } from "vue";
import { NDatePicker as DatePicker } from "naive-ui"

const emit = defineEmits(['update:modelValue', 'saved', 'closed'])
const props = defineProps({
    ...cellProps,
    modelValue: {
        type: [Date, String]
    },
    users: {
        type: Array,
        default() {
            return []
        }
    }
});

const localValue = computed({
    set(value) {
        const date = new Date(value)
        if (formatValue(date, 'date', 'read') != formatValue(props.modelValue, 'date', 'read')) {
            emit('update:modelValue', date)
            emit('saved', 'date')
        } else {
            emit('closed')
        }
    },
    get() {
        return props.modelValue ? formatValue(props.modelValue, 'date', 'read') : null;
    }
})
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
