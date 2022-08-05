<template>
    <div class="flex items-center justify-center tag-select">
        <el-popover
            v-model="state.isOpen"
            placement="bottom-end"
            popper-class='tag-select'
            :width="240"
            :show-arrow="false"
            @after-enter="focusInput()"
        >

            <div class="w-full px-1 pt-2 pb-5">
                <input
                    class="w-full h-8 px-2 border-2 border-gray-100 rounded-md focus:outline-none focus:border-gray-200"
                    type="text"
                    placeholder="Select or create a person"
                    v-model.trim="searchText"
                    ref="input"
                    @click.stop
                    @input="gotoTop"
                    @keydown.enter="selectItem()"
                    @keydown.up.prevent="moveCursorUp()"
                    @keydown.down.prevent="moveCursorDown()"
                />

                <div class="w-full mt-2 space-y-1 overflow-auto items-container max-h-48 ic-scroller" ref="container">
                    <div
                        v-for="tag in filteredList"
                        :key="tag"
                        class="flex items-center px-2 py-2 transition-colors rounded-sm cursor-pointer"
                        :class="[
                            preSelectedValue == tag && 'bg-gray-500 text-white',
                            isSelected(tag.uid) ? 'bg-gray-200 hover:bg-gray-500 hover:text-white' : 'hover:bg-gray-500 hover:text-white'
                        ]"
                        @click.stop="selectItem(tag)"
                    >
                        <el-avatar :src="tag.photoUrl" :size="32">
                            {{ initials(tag.name) }}
                        </el-avatar>
                        <div class="ml-2">
                            <h4 class="mb-0"> {{ tag.name }} </h4>
                            <small> {{ tag.email }}</small>
                        </div>
                    </div>
                </div>

                <div v-if="searchText && filteredList.length == 0 && allowAdd">
                    <button class="w-full h-8 px-2" @click="addItem">
                        Add person:  "{{ searchText}}"
                    </button>
                </div>

                <div v-else-if="filteredList.length == 0 && searchText" class="text-center">
                    <span> This person doesn't exists</span>
                </div>
            </div>


            <template #reference>
            <button
                ref="button"
                :class="{'text-gray-500': formattedItems }"
                class="flex items-center w-full h-full space-x-1 text-xs focus:outline-none"
                @mousedown.prevent
                @focus.prevent="focusButton"
            >
                    <el-avatar v-if="!selectedItems.length" :size="24" class="text-xs transition-colors hover:bg-gray-500">
                        <span class="text-xs"><i class="fa fa-user"></i></span>
                    </el-avatar>
                    <el-avatar
                        v-for="tag in selectedItems.slice(0, limit)"
                        :key="tag.name"
                        :style="{background: tag.color}"
                        :size="24"
                        :title="tag.name"
                        :src="tag.photoUrl"
                        class="mr-1 text-white"
                    >
                        {{ initials(tag.name) }}
                    </el-avatar>
                    <span
                        v-if="moreItems"
                        :title="moreItems"
                        class="px-2 py-1 mr-1 text-white bg-gray-500 rounded-md">
                        + {{ selectedItems.slice(limit).length }}
                    </span>

            </button>
            </template>
        </el-popover>
    </div>
</template>

<script setup>
import { computed, reactive, watch, ref, toRefs } from "vue";
import { useFuseSearch } from "@/utils/useFuseSearch"
import randomcolor from "randomcolor";

const props = defineProps({
    items: {
        type: Array,
        default() {
            return []
        }
    },
    modelValue: {
        type: Array,
        default() {
            return
        }
    },
    limit: {
        type: Number,
        default: 2
    },
    multiple: Boolean,
    placeholder:{
        type: String,
        default: 'Add items'
    },
    allowAdd: {
        type: Boolean,
        default: true
    }
})


const emit = defineEmits({
    'update:modelValue': Array,
    'added': Object,
    'selected': Object
})

const state = reactive({
    cursor: 0,
    isOpen: false
})


const selectedItems = ref([])

watch(() => [...props.modelValue], (value) => {
    selectedItems.value = value.map((item) => {
        item.color = item.color || randomcolor({ luminosity: 'dark'})
        return item;
    })
}, { immediate: true })

const input = ref(null);
const button = ref(null);

// ui
const initials = (name) => {
  return name.split(' ').map( name => name[0]).join('');
}

// Items
const searchText = ref("")
const { items, allowAdd } = toRefs(props)

const formattedItems = computed(() => {
    return selectedItems && selectedItems.value.map(item => item.name).join(" ")
});

const moreItems = computed(() => {
    return selectedItems && selectedItems.value.slice(props.limit).map(item => item.name).join(" ")
});

const { filteredList  } = useFuseSearch(searchText, items,null,  ['name'])

const isSelected = (uid) => {
    return selectedItems.value.find( item => item.uid == uid)
}

// Cursor
const focusInput = () => {
    input.value && input.value.focus()
}

const focusButton = (e) => {
    e.target.click()
}

watch(() => filteredList.value, (items) => {
    if (state.cursor > filteredList.value.length - 1) {
        state.cursor = 0;
    }
})

const preSelectedValue = computed(() => {
    return state.cursor <= filteredList.value.length ? filteredList.value[state.cursor] : "";
});

const container = ref(null);
const gotoTop = () => {
    container.value.scrollTop=0
}

const moveCursorUp = () => {
    if (state.cursor > 0 ) {
        state.cursor = state.cursor - 1
    }
}

const moveCursorDown = () => {
    if ((state.cursor + 1) < filteredList.value.length  ) {
        state.cursor = state.cursor + 1
    }
}

// emits
const addItem = () => {
    if (searchText.value) {
        emit('added', {
            name: searchText.value
        });
        searchText.value = "";
    }
}

const selectItem = (tag) => {
    if (filteredList.value.length) {
        const selected = tag || filteredList.value[state.cursor]
        select(selected)
    } else {
        addItem()
    }
}

const select = (tag) => {
  const index = selectedItems.value.findIndex(item => tag.uid == item.uid)
  if (index < 0 && props.multiple) {
    selectedItems.value.push(tag)
  } else if (index < 0 && !props.multiple) {
    selectedItems.value = [tag]
  } else {
    selectedItems.value.splice(index, 1);
  }

  if (props.multiple) {
      input.value.focus()
  } else {
      state.isOpen = false;
  }

  emit('update:modelValue', selectedItems.value)
}

</script>
