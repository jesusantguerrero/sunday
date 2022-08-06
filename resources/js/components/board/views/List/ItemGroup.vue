<template>
  <div class="ic-list" :data-table-size="tableSize">
    <div class="ic-list__body" :class="{ 'not-expanded': !isExpanded, loaded: isLoaded }">
      <!-- Column title -->
      <div class="ic-list__title">
        <div class="item-false__header sticky_header">
          <div class="flex items-center space-x-2 header-cell item-group-row__header">
            <div class="flex items-center">
              <span class="toolbar-buttons" @click="toggleExpand">
                <i :class="[isExpanded ? 'fa fa-chevron-down' : 'fa fa-chevron-right']" />
              </span>

              <div class="hidden item-group__selector">
                <input
                  type="checkbox"
                  v-model="stage.selected"
                  @change="toggleSelection()"
                />
              </div>

              <span class="font-bold handle" v-if="!isEditMode">
                {{ stage.title || stage.name }}
                {{ isSelectMode ? "(Selection Mode)" : "" }}
              </span>

              <div v-else>
                <input
                  :value="stage.name"
                  type="text"
                  ref="input"
                  @keypress.enter="saveStage(stage)"
                  @blur="saveStage(stage)"
                />
              </div>

              <div class="hidden">
                <i class="mx-2 fa fa-edit" @click="toggleEditMode(true)"></i>
                <NDropdown
                  trigger="click"
                  @select="handleBoardCommands"
                  @click.native.prevent
                  :options="[
                    {
                      key: 'edit',
                      label: 'Edit',
                    },
                    {
                      key: 'delete',
                      label: 'Delete',
                    },
                    {
                      key: 'selection',
                      label: 'Select Mode',
                    },
                  ]"
                >
                  <div
                    class="flex justify-center w-5 h-full py-2 text-center rounded-full hover:bg-gray-200"
                  >
                    <div class="flex items-center justify-center">
                      <i class="fa fa-ellipsis-v"></i>
                    </div>
                  </div>
                </NDropdown>
              </div>
            </div>
            <NDropdown
              trigger="click"
              @select="handleFilterCommands('title', $event)"
              @click.native.prevent
              :options="[
                {
                  key: 'sort',
                  label: 'Sort by Task Name',
                },
                {
                  key: 'clearSort',
                  label: 'Clear sort',
                },
                {
                  key: 'saveOrder',
                  label: 'Save this order',
                },
              ]"
            >
              <div class="px-2 py-1 transition cursor-pointer hover:bg-slate-200">
                <span> {{ items.length }} Tasks </span>
              </div>
            </NDropdown>
          </div>
        </div>
        <div class="false-header"></div>

        <div class="grid" v-if="isExpanded">
          <draggable
            v-model="stage.items"
            @end="saveReorder"
            handle=".handle"
            class="w-full"
          >
            <ItemGroupTitle
              v-for="(item, index) in stage.items"
              class="flex bg-gray-200 border-2 border-white item-false"
              :key="`item-false__title-${item.id}`"
              :item="item"
              :index="index"
              :selected-items="selectedItems"
              :is-select-mode="isSelectMode"
              @selected="handleSelect"
              @saved="saveChanges"
              @command="handleCommand"
            >
            </ItemGroupTitle>
          </draggable>
        </div>
      </div>
      <!-- Column title -->

      <div class="item-group ic-scroller ic-scroller-slim" v-if="isExpanded">
        <div class="grid py-1 text-left item-group-row sticky_header">
          <div
            v-for="field in visibleFields"
            :key="field.name"
            class="item-group-row__header"
          >
            <span v-if="isExpanded" class="font-bold">
              <FieldPopover :field-data="field" :board="board" @saved="onFieldAdded">
                {{ field.title }}
              </FieldPopover>
            </span>
          </div>
        </div>

        <div class="false-header"></div>

        <!-- items  -->
        <template v-if="isExpanded">
          <div
            class="grid text-left item-group-row h-11"
            v-for="(item, index) in items"
            :key="`item-${index}`"
          >
            <div
              v-for="field in visibleFields"
              :key="field.name"
              class="text-center border-2 border-white custom-field"
              :class="[getBg(field, item, field.name)]"
            >
              <ItemGroupCell
                :field-name="field.name"
                :field="field"
                :index="index"
                :item="item"
                @saved="saveChanges(item, field.name, $event)"
              />
            </div>
          </div>
        </template>
        <!-- End of items -->
      </div>

      <!-- column add -->
      <div class="ic-list__add" v-if="isExpanded">
        <div class="item-false__header sticky_header">
          <div class="item-group-row__header">
            <field-popover :field-data="newField" :board="board" @saved="onFieldAdded">
              <i class="fa fa-plus" slot="reference"></i>
            </field-popover>
          </div>
        </div>

        <div class="false-header"></div>

        <div class="grid bg-red-500">
          <div
            class="item-false"
            v-for="item in stage.items"
            :key="`item-false-${item.id}`"
          ></div>
        </div>
      </div>
      <!-- end of column add -->
    </div>

    <!-- new item  -->
    <div class="ic-list__footer">
      <div class="grid grid-cols-10 text-left item-line">
        <div class="flex items-center col-span-12 px-0 item-line-cell">
          <item-group-cell
            class="flex items-center w-full"
            field-name="title"
            :is-title="true"
            :index="-1"
            :item="newItem"
            :is-new="true"
            @saved="newItem['title'] = $event"
            @keydown.enter="addItem(stage)"
          />
        </div>
      </div>
    </div>
    <!-- End of new item -->
  </div>
</template>

<script setup>
import { VueDraggableNext as Draggable } from "vue-draggable-next"
import ItemGroupCell from "../../ItemGroupCell.vue";
import FieldPopover from "../../FieldPopover.vue";
import ItemGroupTitle from './ItemGroupTitle.vue';
import { NDropdown } from "naive-ui"
import { matrixColors } from "@/utils/constants"
import { computed, nextTick, reactive, ref, watch, toRefs } from "vue";
import { Inertia } from "@inertiajs/inertia";


const props = defineProps({
        createMode: {
            type: Boolean
        },
        stage: {
            type: Object
        },
        board: {
            type: Object
        },
        selectedItems: {
            type: Array,
            default() {
                return [];
            }
        },
        items: {
            type: Array,
            default() {
                return [];
            }
        }
});

const emit = defineEmits([])

const state = reactive({
    newItem: {},
    newField: {},
    isSelectMode: false,
    isEditMode: false,
    isExpanded: true,
    isLoaded: false
});

watch(props.selectedItems, () => {
    emit("selected-items-updated", props.selectedItems);
})

const visibleFields = computed(() => {
    return props.board.fields.filter(field => !field.hide);
})

const tableSize = computed(() => {
    return visibleFields.value.length;
});

watch(tableSize, () => {
        const documentRoot = document.documentElement;
        documentRoot.style.setProperty(
            "--board-column-size",
            tableSize.value
        );
    },{
        deep: true,
        immediate: true
});

function getBg(field, item, fieldName) {
    const fieldValue =
        item.fields &&
        item.fields.find(field => field.field_name == fieldName);
    const value = fieldValue ? fieldValue.value : item[fieldName];

    if (value && field.rules) {
        const bgRule = field.rules.find(rule => rule.name == "bg");
        if (bgRule) {
            const ruleOptions =
                bgRule.options || field[bgRule.reference];
            const bg =
                ruleOptions &&
                ruleOptions.find(rule => {
                    const name = rule.name || rule.value;
                    return value.toLowerCase() == name.toLowerCase();
                });
            return bg ? matrixColors[bg.result || bg.color] : "";
        }
    }
    return "bg-gray-200";
}

function toggleExpand() {
    state.isExpanded = !state.isExpanded;
}

const input = ref()
function toggleEditMode() {
    state.isEditMode = !state.isEditMode;
    nextTick(() => {
        if (input.value) {
            input.value.focus();
        }
    });
}
function addItem(stage, reload) {
    const lastItemOrder = Math.max(
        ...props.stage.items.map(item => item.order)
    );
    state.newItem.board_id = stage.board_id;
    state.newItem.stage_id = stage.id;
    state.newItem.fields = props.board.fields.map(field => {
        return {
            field_id: field.id,
            field_name: field.name,
            value: state.newItem[field.name]
        };
    });
    state.newItem.order = lastItemOrder + 1;
    emit("saved", { ...state.newItem }, reload);
    state.newItem = {};
}

function handleSelect() {}

function onFieldAdded() {
    state.newField = {};
    Inertia.reload({ preserveScroll: true });
}

function saveChanges(item, field, value) {
    item[field] = value;
    item.fields = props.board.fields.map(field => {
        return {
            field_id: field.id,
            field_name: field.name,
            value: item[field.name]
        };
    });
    emit("saved", { ...item });
}

function saveStage(stage) {
    stage.name = input.value?.value;
    state.isEditMode = false;
    emit("stage-updated", { ...stage });
}

function saveReorder() {
    prop.stage.items.forEach(async (item, index) => {
        item.order = index;
        emit("saved", { ...item }, false);
    });
    Inertia.reload({ preserveScroll: true });
}

function handleBoardCommands(command) {
    switch (command) {
        case "delete":
            emit("board-deleted", item);
            break;
        case "edit":
            toggleEditMode();
            break;
        case "selection":
            state.isSelectMode = !state.isSelectMode;
            break;
        default:
            break;
    }
}

function handleCommand(item, command) {
    switch (command) {
        case "delete":
            emit("item-deleted", item);
            break;
        case "edit":
            emit("open-item", item);
            break;
        default:
            break;
    }
}

function handleFilterCommands(fieldName, command) {
    switch (command) {
        case "clearSort":
            emit("clearSort", fieldName);
            break;
        case "sort":
            emit("sort", fieldName);
            break;
        default:
            emit("saveOrder", fieldName);
            break;
    }
}

function toggleSelection() {
    props.stage.items.forEach(item => {
        item['selected'] = props.stage.selected
    })
}

const {
    newItem,
    newField,
    isSelectMode,
    isEditMode,
    isExpanded,
    isLoaded
} = toRefs(state);

</script>

<style lang="scss">
.header-cell {
  @apply flex items-center;
  padding-left: 0.25rem;
  height: 34px;
}

.item-line-cell {
  min-height: 35px;
}

.item-group {
  overflow: auto;
}

.ic-list__title,
.item-group,
.ic-list__add {
  position: relative;
}

.ic-list {
  overflow: hidden;
  &__body {
    display: grid;
    grid-template-columns: 1fr 1fr 80px;
    position: relative;

    &.not-expanded {
      @apply bg-gray-200;
      width: 100%;
      display: flex;
    }
  }
}

.item-group-row {
  grid-template-columns: repeat(var(--board-column-size), minmax(180px, 100%));

  &__header {
    @apply text-center;
    height: 34px;
  }
}

.item-checkbox {
  @apply bg-gray-300 mr-2 flex items-center px-2;

  &.selection {
    @apply bg-blue-400;
  }
}

.item-false {
  @apply bg-gray-200;
  height: 44px;
  width: 100%;
  border: 2px solid white;

  &__header {
    @apply text-center font-bold;
    height: 34px;
    margin: 4px;
  }
}

.false-header {
  height: 34px;
  margin: 4px;
  display: none;

  &.active {
    display: block;
  }
}

.sticky-active {
  position: absolute;
  left: 0;
  top: 0;
  background: #f8f8f8 !important;
  width: 100%;
  height: 50px;
  z-index: 1000;
  will-change: transform;
  .item-group-row__header {
    height: 50px;
    width: 100%;
    background: #f8f8f8;
    display: flex;
    justify-content: center;
    align-items: center;

    &.header-cell {
      justify-content: left;
    }
  }

  &.item-false__header {
    margin-left: 0;
    display: flex;
    align-items: center;
  }
}

.item-group__selector {
  @apply flex justify-center items-center mr-2;
  width: 30px;
  height: 100%;
}

.ic-list__footer {
  width: calc(100% - 40px);
  margin-left: auto;
}
</style>
