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
                    :is="component"
                    :users="users"
                    :options="field.options"
                    @saved="saveChanges"
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
                <div class="flex h-full controls" v-if="showControls">
                    <BoardSelector
                        :options="boards"
                        tooltip="Board"
                        icon-class="fas fa-list"
                        :show-label="false"
                        v-model="item.board"
                    />
                    <NTooltip
                        effect="dark"
                        content="reminder date"
                        placement="top"
                    >
                        <i class="mx-2 fas fa-clock"></i>
                    </NTooltip>
                    <NTooltip
                        effect="dark"
                        content="Delegate"
                        placement="top"
                    >
                        <i class="mx-2 fas fa-user"></i>
                    </NTooltip>

                    <NTooltip effect="dark" content="Status" placement="top">
                        <i class="mx-2 fas fa-tag"></i>
                    </NTooltip>
                </div>
            </div>
        </template>
    </div>
</template>

<script>
import { format } from "date-fns";
import LinkPreview from "./cellTypes/LinkPreview.vue";
import InputLabel from "./cellTypes/Label.vue";
import InputDate from "./cellTypes/Date.vue";
import InputPerson from "./cellTypes/Person.vue";
import InputTime from "./cellTypes/Time.vue";
import BoardSelector from './BoardSelector.vue';
import { NTooltip } from "naive-ui";

export default {
    name: "ItemGroupCell",
    inject: ["users"],
    components: {
        InputDate,
        InputLabel,
        InputPerson,
        InputTime,
        LinkPreview,
        BoardSelector
    },
    props: {
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
    },
    watch: {
        item: {
            handler(item) {
                if (item[this.fieldName] != this.value) {
                    const field =
                        item.fields &&
                        item.fields.find(
                            field => field.field_name == this.fieldName
                        );
                    item[this.fieldName] =
                        item[this.fieldName] || (field && field.value);
                    this.value = this.formatValue(
                        item[this.fieldName],
                        this.field ? this.field.type : "default",
                        "read"
                    );
                }
            },
            deep: true,
            immediate: true
        },
        selectValue() {
            if (this.field.type == "person") {
                this.value = this.selectValue;
                this.saveChanges();
            }
        }
    },

    data() {
        return {
            value: "",
            selectValue: "",
            isEditMode: false
        };
    },

    computed: {
        displayValue() {
            return this.formatValue(this.value, this.field.type, "display");
        },
        component() {
            return `input-${this.field.type}`;
        },
        itemStage() {
            return this.item && this.item.stage && this.item.board
                ? `${this.item.stage.name}`
                : "";
        },
        isCustomField() {
            return (
                this.field.type &&
                ["label", "select", "time", "date", "person"].includes(
                    this.field.type
                )
            );
        }
    },

    mounted() {
        // onClickOutside(this.$refs.ItemGroupCell, (e) => {
        //     console.log(e);
        //     const classes = e.path.map(item => item.className)
        //     debugger
        //     const el = classes.includes("el-")
        //     this.isEditMode = false
        // })
    },

    methods: {
        formatValue(value, type = "default", operation = "read") {
            const formatters = {
                date: {
                    write: (value = "") => {
                        return typeof value == "string"
                            ? value
                            : format(value, "yyyy-MM-dd");
                    },
                    read: (value = "") => {
                        return value && typeof value == "string"
                            ? this.setDate(value)
                            : value;
                    },
                    display: (value = "") => {
                        const isString = typeof value == "string";
                        if (isString) {
                            return value;
                        } else {
                            try {
                                return format(value, "yyyy-MM-dd");
                            } catch (e) {
                                return value;
                            }
                        }
                    }
                },
                time: {
                    write: (value = "") => {
                        return typeof value == "string"
                            ? value
                            : format(value, "hh:mm");
                    },
                    read: (value = "") => {
                        const theValue =
                            value && typeof value == "string"
                                ? this.setTime(value)
                                : value;
                        return theValue;
                    },
                    display: (value = "") => {
                        const isString = typeof value == "string";
                        if (isString) {
                            return value;
                        } else {
                            try {
                                return format(value, "hh:mm");
                            } catch (e) {
                                return value;
                            }
                        }
                    }
                },
                label: {
                    display: value => {
                        const option = this.field.options.find(option => {
                            return option.name == this.value;
                        });
                        return option ? option.label || option.name : value;
                    }
                },
                default: {
                    read: value => value,
                    write: value => value,
                    display: value => value
                }
            };
            return formatters[type] && formatters[type][operation]
                ? formatters[type][operation](value)
                : value;
        },

        setDate(dateValue) {
            const date = dateValue ? dateValue.split("-") : null;
            return date ? new Date(date[0], date[1] - 1, date[2]) : null;
        },

        setTime(timeValue) {
            let date = timeValue ? timeValue.split(":") : null;
            const dateTime = new Date();
            dateTime.setHours(date[0]);
            dateTime.setMinutes(date[1]);
            dateTime.setSeconds(0);
            return dateTime;
        },

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

        saveChanges(type = "default") {
            this.$emit("saved", this.formatValue(this.value, type, "write"));
            if (this.closeOnBlur) {
                this.toggleEditMode();
            }
        },

        saveItem($event) {
            this.saveChanges();
            this.$emit("keydown", $event);
            this.toggleEditMode();
        },
    }
};
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
