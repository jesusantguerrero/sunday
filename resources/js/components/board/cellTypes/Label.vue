<template>
  <el-select
    v-model="localValue"
    ref="input"
    placeholder="Select"
    :filterable="true"
    :automatic-dropdown="true"
    @visible-change="!$event && $emit('closed')"
>
    <el-option
        v-for="item in options"
        :key="item.id"
        :label="item.name"
        :value="item.name"
    >
    </el-option>
</el-select>
</template>

<script>
import mixins from "./mixin";

export default {
    mixins: [mixins],
    props: {
        value: {
            type: String
        },
        options: {
            type: Array,
            default() {
                return []
            }
        }
    },
    data() {
        return {
            localValue: ""
        }
    },
    watch: {
        value: {
            handler() {
                this.localValue = this.value;
            },
            immediate: true
        },
        localValue() {
            if (this.localValue != this.value) {
                this.$emit('input', this.localValue)
                this.$emit('saved')
            } else {
                this.$emit('closed')
            }
        }
    }
};
</script>
