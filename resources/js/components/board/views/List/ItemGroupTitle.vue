<template>
    <div
        :class="`item-false bg-gray-200 border-2 border-white flex`"
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
        <div class="flex items-center">
            <i
                class="fa fa-align-justify handle"
            ></i>
        </div>
        <!-- /handle -->

        <item-group-cell
            class="flex items-center"
            field-name="title"
            :select-mode="isSelectMode"
            :index="index"
            :item="item"
            @selected="$emit('selected', $event)"
            @saved="$emit('saved', item, 'title', $event)"
        >
        </item-group-cell>

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
        <el-dropdown
            trigger="click"
            @command="$emit('command', item, $event)"
            @click.native.prevent
        >
            <div
                class="hover:bg-gray-200 w-5 rounded-full py-2 text-center h-full flex justify-center"
            >
                <div class="flex items-center mr-2">
                    <i class="fa fa-ellipsis-v"></i>
                </div>
            </div>
            <el-dropdown-menu slot="dropdown">
                <el-dropdown-item command="edit" icon="fa fa-edit"
                    >Edit</el-dropdown-item
                >
                <el-dropdown-item command="delete" icon="fa fa-trash"
                    >Delete</el-dropdown-item
                >
                <a
                    :href="getFieldValue(item, 'url_id')"
                    target="_blank"
                    rel="noopener noreferrer"
                >
                    <el-dropdown-item
                        command="go"
                        icon="fa fa-external-link-alt"
                        v-if="getFieldValue(item, 'url_id')"
                    >
                        Link by id
                    </el-dropdown-item>
                </a>
                <a
                    :href="getFieldValue(item, 'url_subject')"
                    target="_blank"
                    rel="noopener noreferrer"
                >
                    <el-dropdown-item
                        command="go"
                        icon="fa fa-external-link-alt"
                        v-if="getFieldValue(item, 'url_subject')"
                    >
                        Link by Subject
                    </el-dropdown-item>
                </a>
            </el-dropdown-menu>
        </el-dropdown>
        <!-- /Title options -->
    </div>
</template>

<script>
import ItemGroupCell from "../../ItemGroupCell";

export default {
    components: {
        ItemGroupCell
    },
    props: {
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
    },
    methods: {
        getFieldValue(item, name) {
            const fieldValue = item.fields.find(
                field => field.field_name == name
            );
            return fieldValue && fieldValue.value;
        }
    }
};
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
    margin-left: 40px;
    width: calc(100% - 40px)
}
</style>
