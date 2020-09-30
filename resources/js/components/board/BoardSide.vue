<template>
    <div>
        <h1 class="font-bold flex justify-between items-center">
            <span class="text-3xl"> My Boards </span>
        </h1>
        <div class="mt-5 text-gray-500 flex rounded overflow-hidden">
            <input
                type="search"
                class="w-full p-2"
                placeholder="search boards..."
            />
            <button class="bg-purple-400 text-white p-2" @click="addBoard">
                Search
            </button>
        </div>

        <div class="mt-2">
            <inertia-link
                class="board-item flex px-2 py-1 items-center"
                :class="{ active: isPath(board.link) }"
                :href="board.link"
                v-for="board in boards"
                :key="board.id"
            >
                <span class="w-full block">
                    {{ board.name }}
                </span>

                <button
                    class="bg-grey-400 text-white p-2 hover:bg-red-600"
                    @click.prevent="deleteBoard(board.id)"
                >
                    <i class="fa fa-trash"></i>
                </button>
            </inertia-link>
        </div>
        <div
            class="mt-2 text-gray-500 flex rounded overflow-hidden"
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
            class="mt-2 text-gray-500 flex rounded overflow-hidden"
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
                @click.prevent="addBoard()"
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
                    url: "api/boards",
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
        deleteBoard(id) {
            if (!confirm("Are you sure")) return;
            axios({
                url: `api/boards/${id}`,
                method: "delete"
            }).then(() => {
                this.$inertia.reload();
            });
        }
    }
};
</script>

<style lang="scss">
.board-item {
    @apply my-2 rounded bg-gray-300;

    &:visited {
        @apply text-gray-600;
    }

    &.active {
        @apply bg-purple-400 text-white;
    }
}
</style>
