<template>
  <div class="item-group" :class="{ 'bg-gray-200': !isExpanded }">
    <div>
      <div class="grid grid-cols-11 text-left">
        <div class="col-span-5 header-cell">
          <span class="toolbar-buttons" @click="toggleExpand">
            <i class="fa fa-expand-alt"></i>
          </span>
          <span>
            {{ stage.title || stage.name }}
          </span>
          <span v-if="!isExpanded"> ({{ items.length }} items) </span>
        </div>

        <div
          v-for="field in stage.fields"
          :key="field.name"
          class="text-center"
          :class="[field.name == 'owner' ? 'col-span-2' : '']"
        >
          <span v-if="isExpanded">
            {{ field.title }}
          </span>
        </div>

        <div
          class="text-center"
        >
          <span v-if="isExpanded">
            Actions
          </span>
        </div>
      </div>

      <!-- new item  -->
      <div class="grid grid-cols-10 text-left item-line" v-if="createMode || !items.length">
        <div class="col-span-12 item-line-cell bg-gray-200 flex items-center">
          <item-group-cell
            class="w-full flex items-center"
            field-name="title"
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

      <!-- items  -->
      <template v-if="isExpanded">
        <div
          class="grid grid-cols-11 text-left h-11"
          v-for="(item, index) in items"
          :key="`item-${index}`"
        >
          <div class="col-span-5 bg-gray-200 border-2 border-white flex">
            <div
              class="checkbox-container bg-gray-300 mr-2 flex items-center px-2"
            >
              <input type="checkbox" name="" id="" v-model="item.done" @change="saveChanges(item, 'done', item.done)" :disabled="item.commit_date"/>
            </div>
            <item-group-cell
              class="flex items-center"
              field-name="title"
              :index="index"
              :item="item"
              @saved="saveChanges(item, 'title', $event)"
            >
            </item-group-cell>
          </div>

          <div
            v-for="field in stage.fields"
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
           <button> <i class="fa fa-trash"></i></button>
          </div>
        </div>
        <!-- End of items -->
      </template>
    </div>
  </div>
</template>

<script>
import ItemGroupCell from "./ItemGroupCell";

export default {
  components: {
    ItemGroupCell,
  },
  props: {
    createMode: {
      type: Boolean,
    },
    stage: {
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
      isExpanded: true,
    };
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
    addItem(stage) {
        this.newItem.board_id = stage.board_id
        this.newItem.stage_id = stage.id
        this.newItem.fields = stage.fields.map(field => {
            return {
                field_id: field.id,
                field_name: field.name,
                value: this.newItem[field.name],
            }
        })
        this.$emit("saved", {...this.newItem});
        this.newItem = {};
    },
    saveChanges(item, field, value) {
        item[field] = value;
        item.fields = this.stage.fields.map(field => {
            return {
                field_id: field.id,
                field_name: field.name,
                value: item[field.name],
            }
        })
        this.$emit("saved", {...item});
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
.item-line {
}

.item-line-cell {
  min-height: 35px;
}
</style>
