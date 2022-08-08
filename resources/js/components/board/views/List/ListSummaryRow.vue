<template>
<div class="ic-list__body">
    <div class="ic-list__title" />
    <ListRow
        is-summary
        slim
        :item="summaryRow"
        :row-index="1000"
        :visible-fields="visibleFields"
        :id="`${stage.id}-slim-summary`"
        @scroll="$emit('scroll', $event)"
    />
    <div class="ic-list__add" v-if="isExpanded" />    
</div>
</template>

<script setup>
import { matrixColors } from '@/utils/constants';
import { parseISO } from 'date-fns';
import { computed } from 'vue';
import { formatValue } from '../../cellTypes/mixin';
import ListRow from './ListRow.vue';

const props = defineProps({
    stage: {
        type: Object,
        required: true
    },
    items:{
        type: Array,
    },
    visibleFields: {
        type: Array,
    },
    isExpanded: {
        type: Boolean
    }
})

const defaultFunctions = {
    label: (items, field) => {
        return {
            value: items.reduce((percentages, item) => {
                if (!percentages[item]) {
                    const option = field.options.find(option => option.name == item ) ?? {}
                    percentages[item] = {
                        count: 1 ,
                        color: matrixColors[option.color],
                        label: option.label
                    }
                } else {
                    percentages[item].count++
                }
                return percentages
            }, { }),
            type: 'progress'
        }
    },
    date: (items, fieldName) => {
        const dates = items.reduce((dateByTimes, dateStr) => {
            if (dateStr) {
                const date = formatValue(dateStr, 'date', 'read')
                const timestamp =  date.getTime()
                dateByTimes[timestamp] = date
            }
            return dateByTimes;
        }, {})
        
        return {
            type: 'summaryDate',
            value: {
                min: dates[Math.min(...Object.keys(dates))],
                max: dates[Math.max(...Object.keys(dates))]
            }
        }

    }, 
    default(items, fieldName) {

    }
}


const summaryRow = computed(() => {
    return props.visibleFields.reduce((summary, field) => {
        const method = defaultFunctions[field.type] || defaultFunctions['default']
        const fieldItems = props.items.map(item => item[field.name])
        summary[field.name] = method(fieldItems, field)
        return summary
    }, { })
})
</script>