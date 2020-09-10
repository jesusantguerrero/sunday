<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ board.name }}
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-8xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                     <Board :board="board"></Board>
                </div>
            </div>
        </div>
    </app-layout>
</template>

<script>
    import AppLayout from './../Layouts/AppLayout'
    import Board from "./../components/board";

    export default {
        components: {
            AppLayout,
            Board
        },
        props: {
            board: {
                type: Object,
                default() {
                    return {}
                }
            }
        }
    }
</script>
