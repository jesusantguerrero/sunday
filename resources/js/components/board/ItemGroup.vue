<template>
  <div class="ic-list" :data-table-size="tableSize">
      <div class="ic-list__body" :class="{ 'not-expanded': !isExpanded, 'loaded': isLoaded }">
            <!-- end of column title -->
            <div class="ic-list__title">
                <div class="item-false__header sticky_header">
                    <div class="header-cell item-group-row__header">
                        <span class="toolbar-buttons mr-2" @click="toggleExpand">
                            <i class="fa" :class="[isExpanded ? 'fa-chevron-down' : 'fa-chevron-right']"></i>
                        </span>

                        <span
                            class="font-bold handle"
                            v-if="!isEditMode">
                                {{ stage.title || stage.name }}
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

                        <span v-if="!isExpanded"> ({{ items.length }} items) </span>
                        <i class="fa fa-edit mx-2"  @click="toggleEditMode(true)"></i>
                          <el-dropdown trigger="click" @command="handleBoardCommands" @click.native.prevent>
                                <div class="hover:bg-gray-200 w-5 rounded-full py-2 text-center h-full flex justify-center">
                                    <div class="flex items-center justify-center mr-2">
                                        <i class="fa fa-ellipsis-v"></i>
                                    </div>
                                </div>
                                <el-dropdown-menu slot="dropdown">
                                    <el-dropdown-item command="edit" icon="fa fa-edit">Edit</el-dropdown-item>
                                    <el-dropdown-item command="delete" icon="fa fa-trash">Delete</el-dropdown-item>
                                </el-dropdown-menu>
                            </el-dropdown>
                    </div>
                </div>
                <div class="false-header"></div>

                <div class="bg-red-500 grid" v-if="isExpanded">
                    <draggable v-model="stage.items" @end="saveReorder" handle=".handle" class="w-full">
                        <div :class="`item-false bg-gray-200 border-2 border-white flex`" v-for="(item, index) in stage.items" :key="`item-false__title-${item.id}`">
                            <div
                                class="checkbox-container bg-gray-300 mr-2 flex items-center px-2"
                            >
                                <input type="checkbox" name="" id="" v-model="item.done" @change="saveChanges(item, 'done', item.done)" :disabled="item.commit_date"/>
                            </div>
                            <div class="flex items-center">
                                <i class="fa fa-align-justify handle"></i>
                            </div>
                            <item-group-cell
                                class="flex items-center"
                                field-name="title"
                                :index="index"
                                :item="item"
                                @saved="saveChanges(item, 'title', $event)"
                            >
                            </item-group-cell>
                            <el-dropdown trigger="click" @command="($event) => handleCommand(item, $event)" @click.native.prevent>
                                <div class="hover:bg-gray-200 w-5 rounded-full py-2 text-center h-full flex justify-center">
                                    <div class="flex items-center mr-2">
                                        <i class="fa fa-ellipsis-v"></i>
                                    </div>
                                </div>
                                <el-dropdown-menu slot="dropdown">
                                    <el-dropdown-item command="edit" icon="fa fa-edit">Edit</el-dropdown-item>
                                    <el-dropdown-item command="delete" icon="fa fa-trash">Delete</el-dropdown-item>
                                </el-dropdown-menu>
                            </el-dropdown>
                        </div>
                    </draggable>
                </div>
            </div>
            <!-- End of column title -->

            <div class="item-group ic-scroller ic-scroller-slim" v-if="isExpanded">
                <div class="item-group-row grid py-1  text-left sticky_header">
                    <div
                    v-for="field in visibleFields"
                    :key="field.name"
                    class="item-group-row__header"
                    >
                    <span v-if="isExpanded" class="font-bold">
                        <field-popover :field-data="field" :board="board" @saved="onFieldAdded">
                            {{ field.title }}
                        </field-popover>
                    </span>
                    </div>
                </div>

                <div class="false-header"></div>

                <!-- items  -->
                <template v-if="isExpanded">
                    <div class="grid item-group-row text-left h-11"
                        v-for="(item, index) in items"
                        :key="`item-${index}`">

                        <div
                            v-for="field in visibleFields"
                            :key="field.name"
                            class="custom-field border-white border-2 text-center "
                            :class="[ getBg(field, item, field.name)]"
                        >
                            <item-group-cell
                                :field-name="field.name"
                                :field="field"
                                :index="index"
                                :item="item"
                                @saved="saveChanges(item, field.name, $event)"
                            >
                            </item-group-cell>
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

                <div class="bg-red-500 grid">
                    <div class="item-false" v-for="item in stage.items" :key="`item-false-${item.id}`">

                    </div>
                </div>
            </div>
            <!-- end of column add -->
      </div>

      <!-- new item  -->
      <div class="ic-list__footer">
            <div class="grid grid-cols-10 text-left item-line">
                <div class="col-span-12 item-line-cell px-0 flex items-center">
                <item-group-cell
                    class="w-full flex items-center"
                    field-name="title"
                    :is-title="true"
                    :index="-1"
                    :item="newItem"
                    :is-new="true"
                    @saved="newItem['title'] = $event"
                    @keydown.enter="addItem(stage)"
                >
                </item-group-cell>
                </div>
            </div>
      </div>
     <!-- End of new item -->
  </div>
</template>

<script>
import ItemGroupCell from "./ItemGroupCell";
import Draggable from "vuedraggable";
import FieldPopover from './FieldPopover.vue';

export default {
  components: {
    ItemGroupCell,
    Draggable,
    FieldPopover
  },
  props: {
    createMode: {
      type: Boolean,
    },
    stage: {
      type: Object,
    },
    board: {
      type: Object,
    },
    items: {
      type: Array,
      default() {
          return []
      }
    },
  },
  data() {
    return {
      newItem: {},
      newField: {},
      isEditMode: false,
      isExpanded: true,
      isItemModalOpen: false,
      isLoaded: false
    };
  },
  watch: {
    tableSize: {
        handler() {
            const documentRoot = document.documentElement;
            documentRoot.style.setProperty("--board-column-size", this.tableSize)
        },
        deep: true,
        immediate: true
    }
  },
  computed: {
      visibleFields() {
        return this.board.fields.filter(field => !field.hide)
      },

      tableSize() {
        return this.visibleFields.length;
      }
  },
  methods: {
    getBg(field, item, fieldName) {
      const fieldValue = item.fields && item.fields.find(field => field.field_name == fieldName)
      const value = fieldValue ? fieldValue.value : item[fieldName];

      if (value && field.rules) {
        const bgRule = field.rules.find(rule => rule.name == 'bg');
        if (bgRule) {
            const ruleOptions = bgRule.options || field[bgRule.reference];
            const bg = ruleOptions && ruleOptions.find((rule) => {
                const name = rule.name || rule.value
              return value.toLowerCase() == name.toLowerCase();
            });
            return bg ? "bg-" + (bg.result || bg.color) + "-300" : "";
        }
      }
      return "bg-gray-200";
    },
    toggleExpand() {
      this.isExpanded = !this.isExpanded;
    },
    toggleEditMode() {
      this.isEditMode = !this.isEditMode;
         this.$nextTick(() => {
          if (this.$refs.input) {
              this.$refs.input.focus();
          }
      })
    },
    addItem(stage, reload) {
        const lastItemOrder = Math.max(...this.stage.items.map(item => item.order))
        this.newItem.board_id = stage.board_id
        this.newItem.stage_id = stage.id
        this.newItem.fields = this.board.fields.map(field => {
            return {
                field_id: field.id,
                field_name: field.name,
                value: this.newItem[field.name],
            }
        })
        this.newItem.order = lastItemOrder + 1;
        this.$emit("saved", {...this.newItem}, reload);
        this.newItem = {};
    },

    onFieldAdded() {
        this.newField = {}
        this.$inertia.reload({ preserveScroll: true });
    },

    saveChanges(item, field, value) {
        item[field] = value;
        item.fields = this.board.fields.map(field => {
            return {
                field_id: field.id,
                field_name: field.name,
                value: item[field.name],
            }
        })
        this.$emit("saved", {...item});
    },

    saveStage(stage) {
      stage.name = this.$refs.input.value;
      this.isEditMode = false;
      this.$emit("stage-updated", {...stage});
    },

    saveReorder() {
      this.stage.items.forEach(async (item, index) => {
        item.order = index;
        this.$emit("saved", {...item}, false);
      })
        this.$inertia.reload({ preserveScroll: true })
    },

    handleBoardCommands(command) {
        switch (command) {
            case 'delete':
                this.$emit('board-deleted', item)
                break
            case 'edit':
                this.toggleEditMode();
                break
            default:
            break;
        }
    },

    handleCommand(item, command) {
        switch (command) {
            case 'delete':
                this.$emit('item-deleted', item)
                break
            case 'edit':
                this.$emit('open-item', item)
                break
            default:
            break;
        }
    },
  },
};
</script>

<style lang="scss">
.header-cell {
  @apply flex items-center pl-2 ;
  height: 34px;
}

.item-line-cell {
  min-height: 35px;
}

.item-group {
    overflow: auto;
}

.ic-list__title, .item-group, .ic-list__add {
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
    background: white !important;
    width: 100%;
    height: 50px;
    z-index: 1000;
    will-change: transform;
    .item-group-row__header {
        border-top: 2px solid purple;
        height: 50px;
        width: 100%;
        background: white;
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
</style>
