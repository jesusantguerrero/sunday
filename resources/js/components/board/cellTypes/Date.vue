<template>
<el-date-picker
    v-model="localValue"
    ref="input"
    type="date"
    @blur="$emit('closed')"
    input-class="w-full"
    placeholder="selecciona una fecha"
>
</el-date-picker>
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
                    this.localValue = this.formatValue(this.value, 'date', 'read');
                }
            },
            immediate: true
        },
        localValue() {
             if (this.formatValue(this.localValue, 'date', 'read') != this.formatValue(this.value, 'date', 'read')) {
                 debugger
                this.$emit('input', this.localValue)
                this.$emit('saved', 'date')
            } else {
                this.$emit('closed')
            }
        }
    }
};
</script>
