<template>
  <div>
        <h1 class="font-bold flex justify-between items-center">
            <span class="text-3xl"> My Boards </span>
            <button
                class="rounded-full flex justify-center items-center px-2 h-8 w-8 border-2 border-purple-400 text-purple-400 hover:bg-purple-400 hover:text-white"
                @click="showAdd=!showAdd"
                >

                <i class="fa fa-plus"></i>
                </button>
        </h1>
        <div class="mt-1 text-gray-500 flex rounded overflow-hidden">
            <input type="search" class="w-full p-2" placeholder="search boards...">
            <button class="bg-purple-400 text-white p-2" @click="addBoard"> Search </button>

        </div>
        <div class="mt-6 text-gray-500 flex rounded overflow-hidden" v-if="showAdd">
            <input type="text" class="form-control p-2 w-full" placeholder="Add board name" v-model="boardName">
            <button class="bg-purple-400 text-white p-2" @click="addBoard"> Add</button>
        </div>

        <div class="mt-2">
                <inertia-link
                    class="board-item my-2 p-2 rounded bg-gray-300 block"
                    :href="board.link"
                    v-for="board in boards"
                    :key="board.id">

                    {{ board.name }}
                </inertia-link>
        </div>
  </div>
</template>

<script>
export default {
    props: {
        boards: {
            type: Array,
            default() {
                return []
            }
        }
    },
    data() {
        return {
            boardName: '',
            showAdd: ''
        }
    },
    methods: {
        addBoard() {
            if (this.boardName.trim()) {
                axios({
                    url:'api/boards',
                    method: 'post',
                    data: {
                        name: this.boardName
                    }
                }).then(() => {
                    this.$inertia.reload()
                })
            }
        },

        deleteBoard(id) {
            axios({
                url:`api/boards/${id}`,
                method: 'delete',
            }).then(() => {
                this.$inertia.reload()
            })
        }
    }
}
</script>

<style lang="scss">
.board-item {
    &:visited {
        @apply text-gray-600;
    }
}
</style>
