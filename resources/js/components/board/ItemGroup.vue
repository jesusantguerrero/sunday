<template>
  <div class="item-group" :class="{ 'bg-gray-200': !isExpanded }">
    <div>
      <div class="grid py-1 grid-cols-11 text-left">
        <div :class="`col-span-${titleSize} header-cell`">
            <jet-dropdown align="left" width="48" class="mr-2">
                <template #trigger>
                    <button class="flex text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-gray-300 transition duration-150 ease-in-out">
                        <i class="fa fa-angle-down text-lg"></i>
                    </button>
                </template>

                <template #content>
                    <div class="block px-4 py-2 text-xs text-gray-400">
                        Options
                    </div>
                </template>
            </jet-dropdown>
          <span class="toolbar-buttons mr-2" @click="toggleExpand">
            <i class="fa fa-expand-alt"></i>
          </span>

          <span
            class="font-bold"
            @click="toggleEditMode(true)"
            v-if="!isEditMode">
                {{ stage.title || stage.name }}
          </span>
          <div v-else>
            <input
                :value="stage.name"
                type="text"
                ref="input"
                @blur="saveStage(stage)"
            />

          </div>
          <span v-if="!isExpanded"> ({{ items.length }} items) </span>
        </div>

        <div
          v-for="field in board.fields"
          :key="field.name"
          class="text-center"
          :class="[field.name == 'owner' ? 'col-span-2' : '']"
        >
          <span v-if="isExpanded" class="font-bold">
            {{ field.title }}
          </span>
        </div>

        <div
          class="text-center"
        >
          <span v-if="isExpanded" class="font-bold">
            Actions
          </span>
        </div>
      </div>

      <template v-if="isExpanded">
      <!-- items  -->
        <draggable v-model="stage.items" @end="saveReorder" handle=".handle">
            <transition-group>
                <div
                    class="grid grid-cols-11 text-left h-11"
                    v-for="(item, index) in items"
                    :key="`item-${index}`"
                >
                    <div :class="`col-span-${titleSize} bg-gray-200 border-2 border-white flex`">
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
                    <div class="flex items-center mr-2" @click="$emit('open-item', item)">
                        <i class="fa fa-ellipsis-v"></i>
                    </div>
                    </div>

                    <div
                    v-for="field in board.fields"
                    :key="field.name"
                    class="border-white border-2 text-center item-group-cell w-full"
                    :class="[ getBg(field, item, field.name), field.name == 'owner' ? 'col-span-2' : '']"
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
                    <div
                        class="border-white border-2 text-center item-group-cell w-full flex items-center justify-center bg-gray-400"
                    >
                        <button
                            @click.prevent="$emit('item-deleted', item)"
                            class="w-full h-full bg-gray-400 text-white hover:bg-red-500">
                            <i class="fa fa-trash"></i>
                        </button>
                    </div>
                </div>
            </transition-group>
        </draggable>
        <!-- End of items -->
      </template>

      <!-- new item  -->
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
      <!-- End of new item -->
    </div>
  </div>
</template>

<script>
import ItemGroupCell from "./ItemGroupCell";
import JetDropdown from "../../Jetstream/Dropdown";
import Draggable from "vuedraggable";

export default {
  components: {
    ItemGroupCell,
    Draggable,
    JetDropdown
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
      isEditMode: false,
      isExpanded: true,
      isItemModalOpen: false
    };
  },
  computed: {
      titleSize() {
          return 10 - this.board.fields.length - 1;
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
      this.toggleEditMode();
      this.$emit("stage-updated", {...stage});
    },

    saveReorder() {
      this.stage.items.forEach(async (item, index) => {
        item.order = index;
        this.$emit("saved", {...item}, false);
      })
        this.$inertia.reload({ preserveScroll: true })
    },
  },
};
</script>

<style lang="scss">
.header-cell {
  @apply flex items-center pl-2 ;
  height: 34px;
  .toolbar-buttons {
    display: none;
  }

  &:hover {
    .toolbar-buttons {
      display: inline-flex;
    }
  }
}

.item-line-cell {
  min-height: 35px;
}
</style>
