<template>
    <div
        class="item-group-cell"
        ref="ItemGroupCell"
        :class="{ 'editable-mode': isEditMode, 'new-item': isTitle }"
    >
        <div
            v-if="isTitle && !isEditMode"
            class="new-item-button"
            @click="toggleEditMode()"
        >
            <i class="mr-3 fa fa-plus"></i>
            <span>
                {{ placeholder }}
            </span>
        </div>

        <template v-else-if="!isEditMode">
            <LinkPreview
                v-if="field.type == 'url' && displayValue"
                :value="displayValue"
                @edit="toggleEditMode()"
            />
            <span
                v-else
                @click="toggleEditMode()"
                :title="displayValue"
                class="inline-block w-full px-2 overflow-hidden text-sm border-2 border-transparent border-dashed cursor-pointer h-7 hover:border-gray-300"
            >
                {{ displayValue }}
            </span>
        </template>

        <template v-else>
                <div class="w-full h-8 px-2" v-if="isCustomField">
                    <component
                        v-model="value"
                        ref="input"
                        :is="componentName"
                        :users="users"
                        :options="field.options"
                        @saved="saveChanges()"
                        @closed="isEditMode = false"
                    />
                </div>

                <div v-else class="flex items-center w-full h-full">
                    <div class="controls" v-if="showControls && item.board">
                        <BoardSelector
                            :options="item.board.stages"
                            icon-class="fas fa-layer-group"
                            v-model="item.stage"
                        />
                    </div>
                    <input
                        ref="input"
                        type="text"
                        class="w-full h-8 px-2 mx-0 border-none rounded-none form-input"
                        :class="{ 'new-item': isTitle }"
                        :name="`${index}-${fieldName}`"
                        id=""
                        :placeholder="placeholder"
                        v-model="value"
                        @blur="saveChanges()"
                        @keydown.enter="saveItem"
                    />
                    {{  value }}
                    <div class="flex h-full controls" v-if="showControls">
                        <BoardSelector
                            v-if="boards"
                            :options="boards"
                            tooltip="Board"
                            icon-class="fas fa-list"
                            :show-label="false"
                            v-model="item.board"
                        />
                        <el-Tooltip
                            effect="dark"
                            content="reminder date"
                            placement="top"
                        >
                            <i class="mx-2 fas fa-clock"></i>
                        </el-Tooltip>
                        <ElTooltip
                            effect="dark"
                            content="Delegate"
                            placement="top"
                        >
                            <i class="mx-2 fas fa-user"></i>
                        </ElTooltip>

                        <ElTooltip effect="dark" content="Status" placement="top">
                            <i class="mx-2 fas fa-tag"></i>
                        </ElTooltip>
                    </div>
                </div>
        </template>
    </div>
</template>

<script setup>
import LinkPreview from "./cellTypes/LinkPreview.vue";
import InputLabel from "./cellTypes/Label.vue";
import InputDate from "./cellTypes/Date.vue";
import InputPerson from "./cellTypes/Person.vue";
import InputTime from "./cellTypes/Time.vue";
import CellSummaryProgress from "./cellTypes/CellSummaryProgress.vue";
import BoardSelector from './BoardSelector.vue';
import { NTooltip } from "naive-ui";
import { computed, inject, nextTick, reactive, toRefs, watch, onMounted, ref } from "vue";
import { formatValue } from "./cellTypes/mixin";

const users = inject('users', [])
const props = defineProps({
    fieldName: {
        type: String
    },
    field: {
        type: Object,
        default() {
            return {};
        }
    },
    fieldType: {
        type: String,
        default: "text"
    },
    item: {
        type: Object
    },
    index: {
        type: Number
    },
    isNew: {
        type: Boolean
    },
    isTitle: {
        type: Boolean
    },
    showControls: {
        type: Boolean
    },
    boards: {
        type: Array,
        default() {
            return []
        }
    },
    closeOnBlur: {
        type: Boolean,
        default: true
    },
    placeholder: {
        type: String,
        default: "Add item"
    }
});

const emit = defineEmits(['saved'])

const state = reactive({
    value: "",
    selectValue: "",
    isEditMode: false
});

watch(props.item, (item) => {
    if (item && item[props.fieldName] != state.value) {
        const field = item.fields && item.fields.find(field => field.field_name == props.fieldName);
        item[props.fieldName] = item[props.fieldName] || (field && field.value);
        state.value = formatValue(item[props.fieldName], props.field ? props.field.type : "default", "read");
    }
}, {
    deep: true,
    immediate: true
});

watch(state.selectValue, () => {
    if (props.field.type == "person") {
        state.value = state.selectValue;
        saveChanges();
    }
});

const displayValue = computed(() => {
    return formatValue(state.value, props.field.type, "display",);
});

const components = {
    label: InputLabel,
    date: InputDate,
    person: InputPerson,
    time: InputTime,
    progress: CellSummaryProgress
}

const componentName = computed(() => {
    return components[props.item.type || props.field.type];
});

const isCustomField = computed(() => {
    return (
        props.field.type &&
        ["label", "select", "time", "date", "person"].includes(
            props.field.type
        )
    );
});

const input = ref();
function toggleEditMode() {
    state.isEditMode = !state.isEditMode;
    nextTick(() => {
        if (input.value) {
            const inputEl = input.value.$el && !input.value.focus ? input.value.$el : input.value;
            inputEl.focus();
        }
    });
}

function saveChanges(type = "default") {
    emit("saved", formatValue(state.value, type, "write"));
    if (props.closeOnBlur) {
        toggleEditMode();
    }
}

function saveItem($event) {
    saveChanges();
    state.value = "";
    emit("keydown", $event);
    toggleEditMode();
}

const {
    value,
    isEditMode
} = toRefs(state)
</script>

<style lang="scss">
.el-input__icon {
    line-height: 30px;
}

.el-input__inner {
    height: 30px;
}
.el-date-editor.el-input {
    width: 150px;
}
</style>
<style scoped lang="scss">
.form-input {
    @apply shadow-none appearance-none border w-full py-2 px-3 text-gray-700 leading-tight;
    border-radius: 0 0 0 0 !important;

    &:focus {
        outline: none;
        border: 0;
    }
}

.item-group-cell {
    @apply w-full px-2 h-full flex items-center;
    overflow: hidden !important;
}

.item-group-cell.new-item {
    @apply px-0 my-2 border-white border-t-2 border-b-2;
    position: relative;
    background: transparent !important;
    margin: 0 2px;

    &.editable-mode {
        @apply border-purple-300 border-2;
    }

    .new-item-button {
        @apply p-2 text-gray-300;
        width: 100%;
        transition: all ease 0.3s;
        cursor: pointer;
        &:hover {
            @apply bg-gray-200 text-gray-600;
        }
    }
}
</style>
