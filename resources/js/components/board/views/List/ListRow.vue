<template>
<div
    class="grid text-left item-group-row h-11"
    :class="{'item-group ic-scroller ic-scroller-slim': slim }"
    @scroll="$emit('scroll', $event)"
>
    <component
        :is="getRenderComponent(item[field.name])"
        v-for="(field, index) in visibleFields"
        :key="`${field.name}-${index}`"
        class="text-center border border-white custom-field"
        :class="[getBg(field, item, field.name)]"
        :field-name="field.name"
        :field="field"
        :index="rowIndex"
        :item="item"
        @saved="$emit('saved', item, field.name, $event)"
    />
</div>
</template>

<script setup>
import { matrixColors } from '@/utils/constants';
import CellSummaryDate from '../../cellTypes/CellSummaryDate.vue';
import CellSummaryProgress from '../../cellTypes/CellSummaryProgress.vue';
import ItemGroupCell from "../../ItemGroupCell.vue";

const props = defineProps({
    item: {
        type: Object,
        required: true
    },
    visibleFields: {
        type: Array,
        required: true
    },
    rowIndex: {
        type: Number
    },
    slim: {
        type: Boolean
    },
    isSummary: {
        type: Boolean,
    }
})

function getBg(field, item, fieldName) {
    if(!item || !field || props.isSummary) return
    const fieldValue = item.fields && item.fields.find(field => field.field_name == fieldName);
    const value = fieldValue ? fieldValue.value : item[fieldName];

    if (value && field.rules) {
        const bgRule = field.rules.find(rule => rule.name == "bg");
        if (bgRule) {
            const ruleOptions = bgRule.options || field[bgRule.reference];
            const bg = ruleOptions && ruleOptions.find(rule => {
                const name = rule.name || rule.value;
                return value.toLowerCase() == name.toLowerCase();
            });
            return bg ? matrixColors[bg.result || bg.color] : "";
        }
    }
    return "bg-gray-200";
}

const components = {
    progress: CellSummaryProgress,
    summaryDate: CellSummaryDate
}

const getRenderComponent = (item) => {
    return item?.type ? components[item.type] : ItemGroupCell
}
</script>
