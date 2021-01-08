<template>
    <div class="board-side bg-white py-5">
        <h1 class="font-bold flex justify-between items-centerv px-5">
            <span class="text-2xl"> {{ sectionName }} </span>
        </h1>
        <div class="mt-5 text-gray-500 flex rounded overflow-hidden px-5" v-if="!isHeaderMenu">
            <input
                type="search"
                class="w-full p-2"
                placeholder="search in my boards..."
            />
            <button class="bg-purple-400 text-white p-2">
                Search
            </button>
        </div>

        <div class="mt-2" :class="{'mt-12': isHeaderMenu}">
            <template v-if="!isHeaderMenu">
                <board-side-item
                    @option="handleCommand"
                    :is-active="isPath(board.link)"
                    :board="board"
                    :key="board.link"
                    v-for="board in boards"
                >
                </board-side-item>
            </template>
            <template v-else>
                <board-side-item-link
                    v-for="(section, index) in isHeaderMenu.side"
                    :section="section"
                    :is-active="isPath(section.to)"
                    :key="`${section.to}-${index}`"
                >
                </board-side-item-link>
            </template>

        </div>
        <div
            class="mt-2 text-gray-500 flex rounded overflow-hidden mx-5"
            v-if="!showAdd && !isHeaderMenu"
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
            v-if="showAdd && !isHeaderMenu"
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
import BoardSideItem from "./BoardSideITem";
import BoardSideItemLink from "./BoardSideITemLink";

export default {
    props: {
        boards: {
            type: Array,
            default() {
                return [];
            }
        },
        headerMenu: {
            type: Array,
            default() {
                return [];
            }
        }
    },
    components: {
        BoardSideItem,
        BoardSideItemLink
    },
    data() {
        return {
            boardName: "",
            showAdd: ""
        };
    },
    computed: {
        isHeaderMenu() {
            const headerMenu = this.headerMenu.find( menuItem => this.isPath(menuItem.to, true));
            return headerMenu;
        },
        sectionName() {
           return this.isHeaderMenu ? this.isHeaderMenu.label : "My Boards";
        }
    },
    methods: {
        isPath(url = "", includes) {
            const link = url.replace(window.location.origin, "");
            if (includes) {
                const root = link.split("/");
                const isPath = window.location.pathname.includes(root[1]);
                return isPath
            }
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

        handleCommand(board, command) {
            switch (command) {
                case 'delete':
                    this.confirmDelete(board)
                    break
                default:
                break;
            }
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
.board-side {
    border-radius: 18px 0 0 0;
    height: 100%;
}
.board-item {
    @apply my-2 border-l-4 border-white;

    &:visited {
        @apply text-gray-600;
    }

    &.active {
        @apply border-purple-400 bg-purple-50;
    }

    &__avatar {
        @apply bg-purple-400 flex justify-center items-center;
        width: 30px;
        min-width: 30px;
        font-size: 20px;
        height: 30px;
        border-radius: 8px;
        font-weight: bolder;
        margin-right: 4px;
        color: white;
    }
}
</style>
