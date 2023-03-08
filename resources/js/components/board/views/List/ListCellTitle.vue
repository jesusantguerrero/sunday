<template>
    <div
        :class="`item-false bg-gray-200 border border-white flex`"
        :key="`item-false__title-${item.id}`"
    >
        <!-- Selection Checkbox  -->
        <div class="item-checkbox selection">
            <input type="checkbox" v-model="item.selected" :value="item.id" />
        </div>
        <!-- /Selection Checkbox -->

        <!-- Done Checkbox -->
        <div class="item-checkbox">
            <input
                type="checkbox"
                class="checkbox-done"
                name=""
                id=""
                v-model="item.done"
                @change="$emit('saved', item, 'done', item.done)"
            />
        </div>
        <!-- /Done checkbox -->

        <!-- handle -->
        <div class="flex items-center cursor-grab">
            <div class="handle text-gray-400">
                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" aria-hidden="true" role="img" class="iconify iconify--ic" width="32" height="32" preserveAspectRatio="xMidYMid meet" viewBox="0 0 24 24"><path fill="currentColor" d="M11 18c0 1.1-.9 2-2 2s-2-.9-2-2s.9-2 2-2s2 .9 2 2zm-2-8c-1.1 0-2 .9-2 2s.9 2 2 2s2-.9 2-2s-.9-2-2-2zm0-6c-1.1 0-2 .9-2 2s.9 2 2 2s2-.9 2-2s-.9-2-2-2zm6 4c1.1 0 2-.9 2-2s-.9-2-2-2s-2 .9-2 2s.9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2s2-.9 2-2s-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2s2-.9 2-2s-.9-2-2-2z"></path></svg>
            </div>
        </div>
        <!-- /handle -->

        <ItemGroupCell
            class="flex items-center"
            field-name="title"
            :select-mode="isSelectMode"
            :index="index"
            :item="item"
            @selected="$emit('selected', $event)"
            @saved="$emit('saved', item, 'title', $event)"
        />

        <!-- Estimation Points -->
        <div class="points__container">
            <input
                type="text"
                title="Estimation Points"
                class="points__input"
                v-model="item.points"
                placeholder="0"
                @keydown.enter="$emit('saved', item, 'points', item.points)"
                @blur="$emit('saved', item, 'points', item.points)"
            >
        </div>
        <!-- /Estimation Points -->

        <!-- Title Options -->
        <JetDropdown
            trigger="click"
            @command="$emit('command', item, $event)"
        >
            <template #trigger>
                <div
                    class="flex justify-center w-5 h-full py-2 text-center rounded-full hover:bg-gray-200"
                >
                    <div class="flex items-center mr-2">
                        <i class="fa fa-ellipsis-v"></i>
                    </div>
                </div>
            </template>
            <div>
                <jet-dropdown-link command="edit" icon="fa fa-edit" as="button"
                    >Edit
                </jet-dropdown-link>
                <jet-dropdown-link command="delete" icon="fa fa-trash" as="button">
                    Delete
                </jet-dropdown-link>
                <a
                    :href="getFieldValue(item, 'url_id')"
                    target="_blank"
                    rel="noopener noreferrer"
                >
                    <jet-dropdown-link
                        command="go"
                        icon="fa fa-external-link-alt"
                        v-if="getFieldValue(item, 'url_id')"
                    >
                        Link by id
                    </jet-dropdown-link>
                </a>
                <a
                    :href="getFieldValue(item, 'url_subject')"
                    target="_blank"
                    rel="noopener noreferrer"
                >
                    <jet-dropdown-link
                        command="go"
                        icon="fa fa-external-link-alt"
                        v-if="getFieldValue(item, 'url_subject')"
                    >
                        Link by Subject
                    </jet-dropdown-link>
                </a>
            </div>
        </JetDropdown>
        <!-- /Title options -->
    </div>
</template>

<script setup>
import ItemGroupCell from "../../ItemGroupCell.vue";
import { NPopover as JetDropdown } from "naive-ui"
import JetDropdownLink from "@/Jetstream/DropdownLink.vue"

defineProps({
        item: {
            type: Object,
            required: true
        },
        index: {
            type: Number,
            required: true
        },
        isSelectMode: {
            type: Boolean,
            default: false
        },
        selectedItems: {
            type: Array,
            default() {
                return []
            }
        }
});
function getFieldValue(item, name) {
    const fieldValue = item.fields.find(
        field => field.field_name == name
    );
    return fieldValue && fieldValue.value;
}
</script>

<style lang="scss" scoped>
.points {
    &__container {
        display: flex;
        align-items: center;
        padding: 0 5px;
    }

    &__input {
        @apply border-gray-400 text-gray-400;
        border-width: 1px;
        width: 30px !important;
        height: 30px !important;
        border-radius: 50%;
        text-align: center;
        font-size: 12px;
        font-weight: bold;
        background: transparent;
        transition: all ease .3s;
        cursor: default;

        &:hover, &:focus {
            @apply text-purple-600;
            background: white;
            border: none;
            outline: none;
        }
    }
}

.item-false {
    margin-left: 8px;
    width: calc(100% - 8px)
}
</style>
