<template>
    <AppLayout :boards="boards">
        <div class="pt-12 mx-2 w-12/12">
            <div class="overflow-hidden">
                <BoardSection
                    :board="board"
                    :users="users"
                    :automations="automations"
                />
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
    import AppLayout from '@/Layouts/AppLayout.vue'
    import BoardSection from "@/components/board/BoardSection.vue";

    defineProps({
            boards: {
                type: Array,
                default() {
                    return []
                }
            },
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
            }
        })
</script>
