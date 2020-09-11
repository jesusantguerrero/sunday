<template>
  <div class="item-group-cell w-full px-2 h-full flex items-center"
  :class="{'editable-mode': isEditMode }">
    <span @click="toggleEditMode()" v-if="!isEditMode" class="w-full h-7 inline-block border-2 border-transparent hover:border-gray-300 border-dashed cursor-pointer px-2">
      {{ displayValue }}
    </span>
    <template v-else>
      <select
        name=""
        id=""
        ref="input"
        v-if="['label', 'select'].includes(field.type)"
        v-model="value"
        @blur="saveChanges"
        class="form-input h-8 px-2 rounded-none bg-transparent text-center"
      >
        <option
          :value="option.name"
          v-for="option in field.options"
          :key="option.name"
        >
          {{option.label || option.name }}
        </option>
      </select>
      <input
        v-else
        ref="input"
        @blur="saveChanges"
        type="text"
        class="form-input h-8 px-2 mx-0 rounded-none"
        :name="`${index}-${fieldName}`"
        id=""
        v-model="value"
      />
    </template>
  </div>
</template>

<script>
export default {
  name: "ItemGroupCell",
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
  },
  watch: {
    isNew: {
      handler(isNew) {
        this.isEditMode = isNew;
      },
      immediate: true,
    },
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
              this.$refs.input.focus();
          }
      })
    },
    saveChanges() {
      this.$emit("saved", this.value);
      if (!this.isNew) {
        this.toggleEditMode();
      }
    },
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

// .editable-mode {
//     @apply bg-white;
//     border: 1px solid #6b7280;
// }
</style>
