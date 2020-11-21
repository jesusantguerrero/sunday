<template>
    <app-layout :boards="boards">
        <div class="">
                <div class="w-12/12 mx-2 pt-12">
                    <div class="overflow-hidden">
                        <Board :board="board" :users="users" :automations="automations"></Board>
                    </div>
                </div>
            </div>
    </app-layout>
</template>

<script>
    import AppLayout from './../Layouts/AppLayout'
    import Board from "./../components/board";
    import BoardSide from "../components/board/BoardSide"

    export default {
        components: {
            AppLayout,
            Board,
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
