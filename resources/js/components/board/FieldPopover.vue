<template>
  <Dropdown placement="bottom" width="200" trigger="click">
    <template #trigger>
      <button slot="reference">
        <slot>
          <i class="fa fa-plus" />
        </slot>
      </button>
    </template>
    <div class="field-popover">
      <input
        type="text"
        class="form-control"
        v-model="field.title"
        @keydown.enter="addField()"
      />
      <AtField class="form-group" label="Property type" v-if="!field.id || field.manual">
        <multiselect
          v-model="field.type"
          placeholder="Select"
          ref="input"
          label="title"
          :show-labels="false"
          :select-first="true"
          :filterable="true"
          :automatic-dropdown="true"
          :options="fieldTypes"
        >
        <template v-slot:singleLabel="{ option: item }">
            <div>
              <i :class="item.icon" class="mr-2" />
              <span>{{ item.title }}</span>
            </div>
          </template>
          <template v-slot:option="{ option: item }">
            <div>
              <i :class="item.icon" class="mr-2" />
              <span>{{ item.title }}</span>
            </div>
          </template>
        </multiselect>
      </AtField>
      <ul class="options-list">
        <li class="option-list__item">
            <button class="option-list__button" @click="$emit('sort', field.name)">
                Sort by {{field.name }}
            </button>
        </li>
        <li class="option-list__item" @click="$emit('clear-sort')">
            <button class="option-list__button">Clear sort</button>
        </li>
        <li class="option-list__item">
          <button class="option-list__button">Duplicate</button>
        </li>
        <li class="option-list__item" v-if="field.manual">
          <button @click="deleteField()" class="option-list__button hover:bg-red-300">
            Delete
          </button>
        </li>
        <li class="option-list__item">
          <button @click="addField()" class="option-list__button hover:bg-green-300">
            Save
          </button>
        </li>
      </ul>
    </div>
  </Dropdown>
</template>

<script setup>
// import Dropdown from '@/Jetstream/Dropdown.vue';
import { watch, ref } from "vue";
import { NPopover as Dropdown } from "naive-ui";
import { AtField } from "atmosphere-ui"

const props = defineProps({
  fieldData: {
    type: Object,
    required: true,
  },
  board: {
    type: Object,
    required: true,
  },
});

const emit = defineEmits(['saved'])

const field = ref({});

watch(
  props.fieldData,
  () => {
    field.value = { ...props.fieldData };
    if (field.value.type) {
      field.value.type = "text";
    }
  },
  { immediate: true }
);

const fieldTypes = [
  {
    name: "text",
    icon: "fa fa-align-left",
    title: "Text",
  },
  {
    name: "date",
    icon: "far fa-calendar-alt",
    title: "Date",
  },
  {
    name: "time",
    icon: "far fa-clock",
    title: "Time",
  },
  {
    name: "number",
    icon: "fa fa-hashtag",
    title: "Number",
  },
  {
    name: "label",
    icon: "fa fa-tags",
    title: "Selects",
  },
  {
    name: "person",
    icon: "fa fa-user-friends",
    title: "Person",
  },
];

function addField() {
  const formData = {
    board_id: props.board.id,
    name: field.value.name || field.value.title || `field_${props.board.fields.length}`,
    title: field.value.title || field.value.name,
    type: field.value.type?.name,
    manual: field.value.manual || true,
  };
  let method = "POST";
  let url = "/api/fields";
  if (props.fieldData.id) {
    method = "put";
    url += `/${props.fieldData.id}`;
  }
  axios({
    url,
    method,
    data: formData,
  }).then(() => {
    emit("saved");
  });
}

function deleteField() {
  axios({
    url: `/api/fields/${field.value.id}`,
    method: "delete",
  }).then(() => {
    emit("saved");
  });
}
</script>

<style lang="scss" scoped>
.form-group {
  margin: 5px 0;
  padding: 5px 0;
}

.option-list {
  list-style: none;
  margin: 0;
  padding: 5px 0;
  margin-top: 5px;

  &__item {
    width: 100%;
    height: 34px;
    padding: 5px 0;
  }

  &__button {
    width: 100%;
    height: 34px;
    padding: 0 5px;
    text-align: left;

    &:hover {
      @apply bg-blue-200;
    }
  }
}
</style>
