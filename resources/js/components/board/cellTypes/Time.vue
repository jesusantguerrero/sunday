<template>
<el-time-picker
    v-model="localValue"
    ref="input"
    :picker-options="{
        selectableRange: '00:00:00 - 23:30:59'
    }"
    @change="emitChange"
    @blur="$emit('closed')"
    placeholder="Arbitrary time"
>
</el-time-picker>
</template>

<script>
import mixins from "./mixin";

export default {
    mixins: [mixins],
    props: {
        value: {
            type: [Date, String]
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
                if (this.value) {
                    this.localValue = this.formatValue(this.value, 'time', 'read');
                }
            },
            immediate: true
        },
    },
    methods: {
        emitChange() {
            if (this.formatValue(this.localValue, 'date', 'read') != this.formatValue(this.value, 'date', 'read')) {
                this.$emit('input', this.formatValue(this.localValue, 'time', 'write'))
                this.$emit('saved', 'time')
            } else {
                this.$emit('closed')
            }
        }
    }
};
</script>
