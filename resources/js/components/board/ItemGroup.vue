<template>
  <div class="item-group" :class="{ 'bg-gray-200': !isExpanded }">
    <div>
      <div class="grid grid-cols-12 text-left">
        <div class="col-span-8 header-cell">
          <span class="toolbar-buttons" @click="toggleExpand">
            <i class="fa fa-expand-alt"></i>
          </span>
          <span>
            {{ stage.title }}
          </span>
          <span v-if="!isExpanded"> ({{ items.length }} items) </span>
        </div>
        <div
          v-for="field in stage.fields"
          :key="field.name"
          class="text-center"
        >
          <span v-if="isExpanded">
            {{ field.title }}
          </span>
        </div>
      </div>

      <!-- new item  -->
      <div class="grid grid-cols-12 text-left item-line" v-if="createMode">
        <div class="col-span-8 item-line-cell bg-gray-200 flex">
          <item-group-cell
            class="w-full flex items-center"
            field-name="title"
            :index="-1"
            :item="newItem"
            :is-new="true"
            @saved="newItem['title'] = $event"
          >
          </item-group-cell>
          <div class="bg-gray-300 mr-2 flex items-center px-2">
            <button class="btn" @click="addItem()">
              <i class="fa fa-save"></i>
            </button>
          </div>
        </div>
        <div
          v-for="field in stage.fields"
          :key="field.name"
          class="border-white border-2 p-1 text-center"
          :class="getBg(field, newItem[field.name])"
        >
          <item-group-cell
            :field-name="field.name"
            :index="index"
            :item="newItem"
            :is-new="true"
            @saved="newItem[field.name] = $event"
          >
          </item-group-cell>
        </div>
      </div>
      <!-- End of new item -->

      <!-- items  -->
      <template v-if="isExpanded">
        <div
          class="grid grid-cols-12 text-left"
          v-for="(item, index) in items"
          :key="`item-${index}`"
        >
          <div class="col-span-8 bg-gray-200 border-2 border-white flex">
            <div
              class="checkbox-container bg-gray-300 mr-2 flex items-center px-2"
            >
              <input type="checkbox" name="" id="" />
            </div>
            <item-group-cell
              class="flex items-center"
              field-name="title"
              :index="index"
              :item="item"
              @saved="item['title'] = $event"
            >
            </item-group-cell>
          </div>
          <div
            v-for="field in stage.fields"
            :key="field.name"
            class="border-white border-2 p-1 text-center"
            :class="getBg(field, item[field.name])"
          >
            <item-group-cell
              :field-name="field.name"
              :field="field"
              :index="index"
              :item="item"
              @saved="item[field.name] = $event"
            >
            </item-group-cell>
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
    },
  },
  data() {
    return {
      newItem: {},
      isExpanded: true,
    };
  },
  methods: {
    getBg(field, value) {
      if (value && field.rules && field.rules.bg) {
        const bg = field.rules.bg.find((rule) => {
          return value.toLowerCase() == rule.value.toLowerCase();
        });
        return bg ? "bg-" + bg.result + "-300" : "";
      }
      return "bg-gray-200";
    },
    toggleExpand() {
      this.isExpanded = !this.isExpanded;
    },
    addItem() {
        this.$emit("saved", {...this.newItem});
        this.newItem = {};
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
