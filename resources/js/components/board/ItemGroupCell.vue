<template>
  <div class="item-group-cell">
    <span @dblclick="toggleEditMode()" v-if="!isEditMode">
      {{ displayValue }}
    </span>
    <template v-else>
      <select
        name=""
        id=""
        v-if="field.type == 'select'"
        v-model="value"
        @blur="saveChanges"
        class="form-input"
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
        @blur="saveChanges"
        type="text"
        class="form-input"
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
      if (this.field.type == "select") {
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
  @apply shadow-none appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight;
}
</style>
