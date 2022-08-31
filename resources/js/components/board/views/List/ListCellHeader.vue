<template>
  <div class="sticky_header" v-bind="$attrs">
    <template v-if="visibleFields.length">
      <div
        v-for="field in visibleFields"
        :key="field.name"
        class="item-group-row__header"
      >
        <span class="font-bold">
         <slot :filterIcons="filterIcons[field.name]">
            <FieldPopover
                :field-data="field"
                :board="board"
                @saved="$emit('field-added', $event)"
                @sort="$emit('sort', $event)"
                @clear-sort="$emit('clear-sort')"
            >
                {{ field.title }}
                <i :class="filterIcons[field.name].sort" />
            </FieldPopover>
         </slot>
        </span>
      </div>
    </template>
    <div v-else class="item-group-row__header">
      <span class="font-bold">
        <slot :filterIcons="filterIcons" />
      </span>
    </div>
  </div>
  <div class="false-header"></div>
</template>

<script setup>
import { computed } from "vue";
import FieldPopover from "../../FieldPopover.vue";

const props = defineProps({
  visibleFields: {
    type: Array,
    default: () => ([]),
  },
  filters: {
    type: Object,
    default: () => ({}),
  },
  fieldName: {
    type: String,
  }
});

defineEmits(["field-added"]);

const filterIcons = computed(() => {
    return props.visibleFields.reduce((options, field ) => {
        let sortIcon = ''
        const hasSort = props.filters.sort?.includes(field.name)
        if (hasSort) {
            sortIcon = props.filters.sort?.includes('-') ? 'fas fa-sort-up' : 'fas fa-sort-down'
        }
        options[field.name] = {
            sort: sortIcon
        };
        return options
    }, {})
})
</script>
