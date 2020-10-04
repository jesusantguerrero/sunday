<template>
    <app-layout>
        <div class="">
            <div class="max-w-8xl mx-auto sm:pr-6 lg:pr-8 flex">
                <div class="mr-4 min-h-screen bg-white w-80">
                    <board-side
                        :boards="boards"
                    >
                    </board-side>
                </div>

                <div class="w-10/12 mx-2 pt-12">
                    <div class="overflow-hidden">
                        <Board :board="board" :users="users"></Board>
                    </div>
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
            users: {
                type: Array,
                refault() {
                    return []
                }
            }
        }
    }
</script>
