<template>
    <app-layout :boards="boards">
        <div class="">
                <div class="w-12/12 mx-2 pt-12">
                    <div class="overflow-hidden">
                        <board-section 
                            :board="board" 
                            :users="users" 
                            :automations="automations"
                            :filters="filters"
                        />
                    </div>
                </div>
            </div>
    </app-layout>
</template>

<script>
    import AppLayout from './../Layouts/AppLayout'
    import BoardSection from "./../components/board/BoardSection";
    import BoardSide from "../components/board/BoardSide"

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
                default() {
                    return []
                }
            },
            filters: {
                type: Object,
                default() {
                    return {}
                }
            }
        }
    }
</script>
