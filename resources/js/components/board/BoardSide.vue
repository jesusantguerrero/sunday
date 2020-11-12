<template>
    <div class="bg-white py-5">
        <h1 class="font-bold flex justify-between items-centerv px-5">
            <span class="text-2xl"> My Boards </span>
        </h1>
        <div class="mt-5 text-gray-500 flex rounded overflow-hidden px-5">
            <input
                type="search"
                class="w-full p-2"
                placeholder="search boards..."
            />
            <button class="bg-purple-400 text-white p-2">
                Search
            </button>
        </div>

        <div class="mt-2">
            <inertia-link
                class="board-item flex px-2 py-1 items-center px-5"
                :class="{ active: isPath(board.link) }"
                :href="board.link"
                v-for="board in boards"
                :key="board.id"
            >
                <span class="board-item__avatar">
                    {{ board.name.slice(0, 1) }}
                </span>

                <span class="w-full block">
                    {{ board.name }}
                </span>

                <button
                    class="bg-grey-400 text-white p-2 hover:bg-red-600"
                    @click.prevent="confirmDelete(board)"
                >
                    <i class="fa fa-trash"></i>
                </button>
            </inertia-link>
        </div>
        <div
            class="mt-2 text-gray-500 flex rounded overflow-hidden mx-5"
            v-if="!showAdd"
        >
            <button
                class="flex justify-center items-center px-2 h-10 w-full border-2 border-purple-400 text-purple-400 hover:bg-purple-400 hover:text-white"
                @click="showAddForm"
            >
                <i class="fa fa-plus"></i>
            </button>
        </div>
        <div
            class="mt-2 text-gray-500 flex rounded overflow-hidden mx-5"
            v-if="showAdd"
        >
            <input
                type="text"
                class="form-control p-2 w-full"
                placeholder="Add board name"
                v-model="boardName"
                ref="input"
                @keydown.enter="addBoard()"
                @blur="showAdd = false"
            />
            <button
                class="bg-purple-400 text-white p-2"
                @click.native="addBoard()"
            >
                Add
            </button>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        boards: {
            type: Array,
            default() {
                return [];
            }
        }
    },
    data() {
        return {
            boardName: "",
            showAdd: ""
        };
    },
    methods: {
        isPath(url) {
            const link = url.replace(window.location.origin, "");
            return link == window.location.pathname;
        },
        addBoard() {
            if (this.boardName.trim()) {
                axios({
                    url: "/api/boards",
                    method: "post",
                    data: {
                        name: this.boardName
                    }
                }).then(() => {
                    this.$inertia.reload();
                    this.boardName = "";
                    this.showAdd = false;
                });
            }
        },

        showAddForm() {
            this.showAdd = true;
            this.$nextTick(() => {
                this.$refs.input.focus();
            });
        },

        confirmDelete(board) {
            this.showConfirm({
                title: `Delete "${board.name}" board`,
                content: "Are you sure you want to delete this board?",
                confirmationButtonText: "Yes, delete",
                confirm: () => {
                    this.deleteBoard(board.id)
                }
            })
        },

        deleteBoard(id) {
            axios({
                url: `/api/boards/${id}`,
                method: "delete"
            }).then(() => {
                const index = this.boards.findIndex(board => board.id == id);
                const currentBoard = index >= 0 ? this.boards[index] : null;

                if (currentBoard && this.isPath(currentBoard.link)) {
                    if (index != 0) {
                        const previousBoard = this.boards[index - 1];
                        return this.$inertia.visit(previousBoard.link)
                    }
                    return this.$inertia.visit('dashboard')
                }
                 this.$inertia.reload();
            }).catch((err) => {
                console.log(err)
            });
        }
    }
};
</script>

<style lang="scss">
.board-item {
    @apply my-2 border-l-4 border-white;

    &:visited {
        @apply text-gray-600;
    }

    &.active {
        @apply border-purple-400 bg-purple-50;
        &__avatar {
            @apply bg-purple-400 flex justify-center items-center;
            width: 40px;
            min-width: 40px;
            font-size: 30px;
            height: 40px;
            border-radius: 8px;
            font-weight: bolder;
            margin-right: 4px;
            color: white;
        }
    }

    &__avatar {
        @apply bg-purple-400 flex justify-center items-center;
        width: 40px;
        min-width: 40px;
        font-size: 30px;
        height: 40px;
        border-radius: 8px;
        font-weight: bolder;
        margin-right: 4px;
        color: white;
    }
}
</style>
