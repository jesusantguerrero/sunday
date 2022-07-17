<template>
    <app-layout :boards="boards">
        <div class="">
                <div class="pt-12 mx-2 w-12/12">
                    <div class="overflow-hidden">
                        <board-section :board="board" :users="users" :automations="automations"></board-section>
                    </div>
                </div>
            </div>
    </app-layout>
</template>

<script>
    import AppLayout from './../Layouts/AppLayout.vue'
    import BoardSection from "./../components/board/BoardSection.vue";
    import BoardSide from "../components/board/BoardSide.vue"

    export default {
        components: {
            AppLayout,
            BoardSection,
            BoardSide
        },
        props: {
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
        }
    }
</script>
