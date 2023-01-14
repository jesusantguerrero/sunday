<template>
  <div class="cursor-pointer">
    <div class="view" v-if="!isEditMode" @click="toggleEditMode()">
      <ElTooltip
        effect="dark"
        :content="`${tooltip}: ${modelValue.name}`"
        placement="top"
        v-if="modelValue"
      >
        <div class="flex items-center">
          <i :class="`${iconClass} mx-2`"></i>
          <div class="ml-2 font-bold" v-if="showLabel">
            {{ modelValue.name }}
          </div>
        </div>
      </ElTooltip>
    </div>
    <div class="selector" v-else>
      <ElSelect
        v-model="localValue"
        ref="input"
        placeholder="Select"
        :filterable="true"
        :automatic-dropdown="true"
        @visible-change="!$event && toggleEditMode()"
      >
        <ElOption
          v-for="item in options"
          :key="item.id"
          :label="item?.name"
          :value="item"
        />
      </ElSelect>
      Here
    </div>
  </div>
</template>

<script setup>
import { ref, watch, nextTick } from "vue";


const props = defineProps({
  options: {
    type: Array,
    required: true,
  },
  modelValue: {
    type: Object,
    required: true,
  },
  iconClass: {
    type: String,
    default: "fas fa-layer-group",
  },
  tooltip: {
    type: String,
    default: "Stage",
  },
  showLabel: {
    type: Boolean,
    default: true,
  },
});

const emit = defineEmits(['update:modelValue'])

const localValue = ref({});
const isEditMode = ref(false);

watch(
  () => props.modelValue,
  (value) => {
    localValue.value = value;
  },
  {
    immediate: true,
  }
);

watch(localValue, (value) => {
  if (localValue.value !== value) {
    emit("update:modelVnput", this.localValue);
  }
});

const input = ref();
const toggleEditMode = () => {
  isEditMode.value = !isEditMode.value;
  nextTick(() => {
    if (input.value) {
      const inputRef =
        input.value.$el && !input.value.focus ? input.value.$el : input.value;
      inputRef.focus();
    }
  });
};
</script>
