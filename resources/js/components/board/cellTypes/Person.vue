<template>
    <NSelect
        v-model:value="localValue"
        ref="input"
        value-key="name"
        placeholder="Select"
        :filterable="true"
        :automatic-dropdown="true"
        :options="users"
        @blur="$emit('closed')"
    />
</template>

<script setup>
import { cellProps }from "./mixin";
import { NSelect } from "naive-ui"
import { computed } from "vue"

const emit = defineEmits(['update:modelValue', 'closed', 'saved']);

const props = defineProps({
    ...cellProps,
    modelValue: {
        type: String
    },
    users: {
        type: Array,
        default() {
            return []
        }
    }
});

const localValue = computed({
    get() {
        return this.modelValue;
    },
    set(value) {
        if (value != props.modelValue) {
            emit('update:modelValue', value)
            emit('saved')
        } else {
            emit('closed')
        }
    }
});
</script>

<style lang="scss">
.item-group-cell {
    .el-select .el-input.is-focus .el-input__inner {
        border: none;
    }

    .el-input__inner {
        height: 33px;
    }

    .el-select {
        width: 100%;
    }
}
</style>
