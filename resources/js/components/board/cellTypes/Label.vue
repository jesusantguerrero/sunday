<template>
  <NSelect
    v-model:value="localValue"
    ref="input"
    placeholder="Select"
    :filterable="true"
    :automatic-dropdown="true"
    :options="options"
    track-by="id"
    label="label"
    value-field="name"
    @blur="$emit('closed')"
/>
</template>

<script>
import mixins from "./mixin";
import { NSelect } from "naive-ui"

export default {
    mixins: [mixins],
    props: {
        modelValue: {
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
        modelValue: {
            handler(value) {
                this.localValue = value;
            },
            immediate: true
        },
        localValue() {
            if (this.localValue != this.modelValue) {
                this.$emit('update:modelValue', this.localValue)
                this.$emit('saved')
            } else {
                this.$emit('closed')
            }
        }
    },
    mounted() {
        console.log(this.options)
    },
    components: {
        NSelect
    }
};
</script>
