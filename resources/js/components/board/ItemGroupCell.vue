<template>
    <div
        class="item-group-cell"
        :class="{ 'editable-mode': isEditMode, 'new-item': isTitle }"
    >
        <div
            v-if="isTitle && !isEditMode"
            class="new-item-button"
            @click="toggleEditMode()"
        >
            <i class="fa fa-plus mr-3"></i>
            <span>
                Add Item
            </span>
        </div>

        <span
            @click="toggleEditMode()"
            v-else-if="!isEditMode"
            class="w-full h-7 text-sm inline-block border-2 border-transparent hover:border-gray-300 border-dashed cursor-pointer px-2"
        >
            {{ displayValue }}
        </span>

        <template v-else>
            <div class="h-8 px-2" v-if="['person'].includes(field.type)">
                <el-select
                    v-model="selectValue"
                    placeholder="Select"
                    ref="input"
                    value-key="name"
                    :filterable="true"
                    :automatic-dropdown="true"
                    @visible-change="!$event && saveChanges()"
                >
                    <el-option
                        v-for="item in users"
                        :key="item.id"
                        :label="item.name"
                        :value="item.name"
                    >
                    </el-option>
                </el-select>
            </div>

            <div class="h-8 px-2" v-else-if="['date'].includes(field.type)">
                <el-date-picker
                    ref="input"
                    v-model="value"
                    type="date"
                    @input="saveChanges('date')"
                    @blur="isEditMode=false"
                    input-class="w-full"
                    placeholder="selecciona una fecha"
                >
                </el-date-picker>
            </div>

            <div class="h-8 px-2" v-else-if="['time'].includes(field.type)">
                <el-time-picker
                    ref="input"
                    v-model="value"
                    :picker-options="{
                        selectableRange: '00:00:00 - 23:30:59'
                    }"
                    @change="saveChanges('time')"
                    @blur="isEditMode=false"
                    placeholder="Arbitrary time"
                >
                </el-time-picker>
            </div>

            <div
                class="h-8 px-2"
                v-else-if="['label', 'select'].includes(field.type)"
            >
                <el-select
                    v-model="value"
                    placeholder="Select"
                    ref="input"
                    @change="saveChanges()"
                    @visible-change="!$event && saveChanges()"
                    :filterable="true"
                    :automatic-dropdown="true"
                >
                    <el-option
                        v-for="item in field.options"
                        :key="item.id"
                        :label="item.name"
                        :value="item.name"
                    >
                    </el-option>
                </el-select>
            </div>

            <input
                v-else
                ref="input"
                @blur="saveChanges()"
                type="text"
                class="form-input h-8 px-2 mx-0 rounded-none"
                :class="{ 'new-item': isTitle }"
                :name="`${index}-${fieldName}`"
                id=""
                v-model="value"
                @keydown.enter="saveItem"
            />
        </template>
    </div>
</template>

<script>
import { format, toDate } from "date-fns";

export default {
    name: "ItemGroupCell",
    inject: ["users"],
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
            if (["select", "label"].includes(this.field.type)) {
                const option = this.field.options.find(option => {
                    return option.name == this.value;
                });
                return option
                    ? option.label || option.name
                    : this.item[this.fieldName];
            } else if (["date"].includes(this.field.type)) {
                if (this.item[this.fieldName]) {
                    return typeof this.item[this.fieldName] == "string"
                        ? this.item[this.fieldName]
                        : format(
                              new Date(this.item[this.fieldName]),
                              "yyyy-MM-dd"
                          );
                }
                return "";
            } else {
                return this.item[this.fieldName];
            }
        }
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
                    }
                },
                time: {
                    write: (value = "") => {
                        return typeof value == "string"
                            ? value
                            : format(value, "hh:mm");
                    },
                    read: (value = "") => {
                        const theValue = value && typeof value == "string"
                            ? this.setTime(value)
                            : value;
                        return theValue;
                    }
                },
                default: {
                    read: value => value,
                    write: value => value
                }
            };
            return formatters[type]
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
            this.toggleEditMode();
        },
        saveItem($event) {
            this.saveChanges();
            this.$emit("keydown", $event);
            this.toggleEditMode();
        }
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
