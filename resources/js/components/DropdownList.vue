<template>
<Dropdown align="left" width="48">
    <template #trigger>
        <slot name="trigger" />
    </template>

    <template #content>
        <div>
            <template v-for="(action, index) in actions">
                <div class="border-t border-gray-100" v-if="!isDivider(action)" :key="`divider-action-${index}`" />
                <DropdownLink :href="action.href"  :key="`action-${index}`" v-else-if="action.href">
                    {{ action.label }}
                </DropdownLink>
                <button :key="`button-action-${index}`" v-else class="w-full px-5 py-1 left">
                    {{ action.label }}
                </button>
            </template>
        </div>
    </template>
</Dropdown>
</template>

<script>
import DropdownLink from '../Jetstream/DropdownLink.vue';
import Dropdown from '../Jetstream/Dropdown.vue';

export default {
    name: "DropdownList",
    props: {
        actions: {
            type: Array,
            default() {
                return []
            }
        },
    },
    components: {
        Dropdown,
        DropdownLink
    },
    methods: {
        isDivider(action) {
            return typeof action == 'object';
        }
    },
}
</script>
