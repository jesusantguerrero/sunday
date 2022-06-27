<template>
    <div class="py-5 bg-white board-side">
        <WorkspaceSelector
            :current-workspace="$page.props.user.current_workspace"
            :workspaces="$page.props.user.all_workspaces"
        />
        <SearchBar v-model="search" class="mt-5" v-if="!isHeaderMenu" />

        <div class="mt-2" :class="{'mt-12': isHeaderMenu}">
            <template v-if="!isHeaderMenu">
                <board-side-item
                    @option="handleCommand"
                    :is-active="isPath(board.link)"
                    :board="board"
                    :key="board.link"
                    v-for="board in filteredBoards"
                />
            </template>
            <template v-else>
                <board-side-item-link
                    v-for="(section, index) in isHeaderMenu.side"
                    :section="section"
                    :is-active="isPath(section.to)"
                    :key="`${section.to}-${index}`"
                />
            </template>

        </div>
        <div
            class="flex mx-5 mt-2 overflow-hidden text-gray-500 rounded"
            v-if="!showAdd && !isHeaderMenu"
        >
            <button
                class="flex items-center justify-center w-full h-10 px-2 text-purple-400 border-2 border-purple-400 hover:bg-purple-400 hover:text-white"
                @click="openBoardForm()"
            >
                <i class="fa fa-plus"></i>
            </button>
        </div>
        <div
            class="flex mx-5 mt-2 overflow-hidden text-gray-500 rounded"
            v-if="showAdd && !isHeaderMenu"
        >
            <input
                type="text"
                class="w-full p-2 form-control"
                placeholder="Add board name"
                v-model="boardName"
                ref="input"
                @keydown.enter="addBoard()"
                @blur="showAdd = false"
            />
            <button
                class="p-2 text-white bg-purple-400"
                @click.native="addBoard()"
            >
                Add
            </button>
        </div>

        <board-form-modal
            :record-data="boardData"
            :is-open="isBoardFormOpen"
            @saved="addBoard"
            @cancel="isBoardFormOpen=false"
        />
    </div>
</template>

<script>
import BoardSideItem from "./BoardSideITem";
import BoardSideItemLink from "./BoardSideITemLink";
import BoardFormModal from "./BoardForm"
import SearchBar from "../SearchBar.vue";
import WorkspaceSelector from "../workspace/WorkspaceSelector.vue";

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
    BoardSideItemLink,
    BoardFormModal,
    SearchBar,
    WorkspaceSelector
},
    data() {
        return {
            boardName: "",
            showAdd: "",
            search: "",
            isBoardFormOpen: false,
            boardData: null
        };
    },
    computed: {
        isHeaderMenu() {
            const headerMenu = this.headerMenu.find( menuItem => this.isPath(menuItem.to, true));
            return headerMenu;
        },
        filteredBoards() {
            return this.boards.filter(board => board.name.toLowerCase().includes(this.search.toLowerCase()));
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
        openBoardForm(board = {}) {
            this.isBoardFormOpen = true;
            this.boardData = board;
        },
        addBoard(board) {
            this.isBoardFormOpen = false;
            this.$inertia.reload();
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
