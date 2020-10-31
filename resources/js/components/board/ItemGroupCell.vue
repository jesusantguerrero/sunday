<template>
  <div class="item-group-cell w-full px-2 h-full flex items-center"
  :class="{'editable-mode': isEditMode, 'new-item': isTitle }">

    <div v-if="isTitle && !isEditMode" class="new-item-button" @click="toggleEditMode()">
        <i class="fa fa-plus mr-3"></i>
        <span>
        Add Item
        </span>
    </div>

    <span @click="toggleEditMode()" v-else-if="!isEditMode" class="w-full h-7 text-sm inline-block border-2 border-transparent hover:border-gray-300 border-dashed cursor-pointer px-2">
      {{ displayValue }}
    </span>

    <template v-else>
      <div class="h-8 px-2"
        v-if="['person'].includes(field.type)"
      >
        <multiselect
            v-model="selectValue"
            ref="input"
            label="name"
            track-by="id"
            :show-labels="false"
            :options="users"
            class="w-full"
            @select="value = $event.name"
            @close="saveChanges"
        >
        </multiselect>
      </div>

      <div class="h-8 px-2"
        v-else-if="['date'].includes(field.type)"
      >
        <v-date-picker v-model="value" @dayclick="saveChanges"/>
      </div>

      <div class="h-8 px-2"
        v-else-if="['label', 'select'].includes(field.type)"
      >
        <multiselect
            v-model="selectValue"
            ref="input"
            label="name"
            track-by="id"
            :show-labels="false"
            :options="field.options"
            class="w-full"
            @select="value = $event.name"
            @close="saveChanges"
        >
        </multiselect>
      </div>

      <input
        v-else
        ref="input"
        @blur="saveChanges"
        type="text"
        class="form-input h-8 px-2 mx-0 rounded-none"
        :class="{'new-item': isTitle }"
        :name="`${index}-${fieldName}`"
        id=""
        v-model="value"
        @keydown.enter="saveItem"
      />
    </template>
  </div>
</template>

<script>
import { format } from 'date-fns';

export default {
  name: "ItemGroupCell",
  inject: ['users'],
  props: {
    fieldName: {
      type: String,
    },
    field: {
      type: Object,
      default() {
        return {};
      },
    },
    fieldType: {
      type: String,
      default: "text",
    },
    item: {
      type: Object,
    },
    index: {
      type: Number,
    },
    isNew: {
      type: Boolean,
    },
    isTitle: {
      type: Boolean,
    },
  },
  watch: {
    item: {
      handler(item) {
        if (item[this.fieldName] != this.value) {
            const field = item.fields && item.fields.find(field => field.field_name == this.fieldName)
            item[this.fieldName] = item[this.fieldName] || field && field.value;
            this.value = item[this.fieldName];
        }
      },
      deep: true,
      immediate: true,
    },
  },
  data() {
    return {
      value: "",
      selectValue: "",
      isEditMode: false,
    };
  },
  computed: {
    displayValue() {
      if (["select", "label"].includes(this.field.type)) {
        const option = this.field.options.find((option) => {
          return option.name == this.value;
		});
		return option ? option.label || option.name :  this.item[this.fieldName];
      } else if (["date"].includes(this.field.type)) {
          return this.item[this.fieldName] ? format(new Date(this.item[this.fieldName]), 'yyyy-MM-dd') : '';
      } else {
        return this.item[this.fieldName];
      }
    },
  },
  methods: {
    toggleEditMode() {
      this.isEditMode = !this.isEditMode;
      this.$nextTick(() => {
          if (this.$refs.input) {
              const input = this.$refs.input.$el ? this.$refs.input.$el : this.$refs.input;
              input.focus();
          }
      })
    },
    saveChanges() {
      this.$emit("saved", this.value);
      this.toggleEditMode();
    },
    saveItem($event) {
        this.saveChanges();
        this.$emit('keydown', $event)
        this.toggleEditMode()
    }
  },
};
</script>

<style scoped lang="scss">
.form-input {
  @apply shadow-none appearance-none border w-full py-2 px-3 text-gray-700 leading-tight;
  border-radius: 0 0 0 0 !important;

  &:focus {
      outline: none;
      border: 0;
  }

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
        transition: all ease .3s;
        cursor: pointer;
        &:hover {
            @apply bg-gray-200 text-gray-600;
        }
    }
}
</style>
