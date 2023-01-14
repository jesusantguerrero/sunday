<template>
    <AppLayout :boards="boards">
        <BoardSection
            class="pt-12 mx-2 w-full overflow-hidden"
            :board="board"
            :users="users"
            :automations="automations"
            :filters="filters"
        />
    </AppLayout>
</template>

<script setup>
    import AppLayout from '@/Layouts/AppLayout.vue'
    import BoardSection from "@/components/board/BoardSection.vue";

    defineProps({
        board: {
            type: Object,
            default() {
                return {}
            }
        },
        automations: {
            type: Array,
            refault() {
                return []
            }
        },
        users: {
            type: Array,
            refault() {
                return []
            }
        },
        filters: {
            type: Object,
        }
    })
</script>
