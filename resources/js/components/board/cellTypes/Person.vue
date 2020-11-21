<template>
    <el-select
        v-model="localValue"
        ref="input"
        value-key="name"
        placeholder="Select"
        :filterable="true"
        :automatic-dropdown="true"
        @visible-change="($event) => !$event && $emit('closed')"
    >
        <el-option
            ref="input"
            v-for="item in users"
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
        users: {
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
