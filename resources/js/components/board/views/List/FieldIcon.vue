<template>
    <i :class="sortIcon" />
</template>

<script>
export default {
    props: {
        field: {
    
        },
        filters: {
            type: Object,
            default() {
                return {}
            }
        }
    },
    computed: {
        sortIcon() {
            let sortIcon = ''
            const hasSort = this.filters.sort?.includes(this.field)
            if (hasSort) {
                sortIcon = this.filters.sort?.includes('-') ? 'fas fa-sort-up' : 'fas fa-sort-down'
            }
            return sortIcon
        }
    }
}
</script>
