<template>
    <div>
        <div class="view" v-if="!isEditMode"   @click="toggleEditMode()">
            <el-tooltip effect="dark" :content="`${tooltip}: ${value.name}`" placement="top">
                <div class="flex items-center">
                    <i :class="`${iconClass} mx-2`"></i>
                    <div class="ml-2 font-bold" v-if="showLabel">
                        {{ value.name }}
                    </div>
                </div>
            </el-tooltip>
        </div>
        <div class="selector" v-else>
            <el-select
                v-model="localValue"
                ref="input"
                placeholder="Select"
                :filterable="true"
                :automatic-dropdown="true"
                @visible-change="!$event && toggleEditMode()"
            >
                <el-option
                    v-for="item in options"
                    :key="item.id"
                    :label="item.name"
                    :value="item"
                >
                </el-option>
            </el-select>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        options: {
            type: Array,
            required: true
        },
        value: {
            type: Object,
            required: true
        },
        iconClass: {
            type: String,
            default: "fas fa-layer-group"
        },
        tooltip: {
            type: String,
            default: "Stage"
        },
        showLabel: {
            type: Boolean,
            default: true
        }
    },
    data() {
        return {
            localValue: {},
            isEditMode: false
        };
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
            }
        }
    },
    methods: {
          toggleEditMode() {
            this.isEditMode = !this.isEditMode;
            this.$nextTick(() => {
                if (this.$refs.input) {
                    const input =
                        this.$refs.input.$el && !this.$refs.input.focus
                            ? this.$refs.input.$el
                            : this.$refs.input;
                    input.focus();
                }
            });
        },
    }
};
</script>

<style></style>
