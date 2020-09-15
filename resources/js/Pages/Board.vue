<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ board.name }}
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-8xl mx-auto sm:px-6 lg:px-8 flex">
                <div class="w-2/12 mr-4">
                    <board-side
                        :boards="boards"
                    >
                    </board-side>
                </div>

                <div class="w-10/12 mx-2">
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                        <Board :board="board"></Board>
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
            }
        }
    }
</script>
