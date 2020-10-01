<template>
  <div class="px-8 pb-24">
    <div class="board__toolbar flex justify-between mb-5">
      <div class="flex text-left">
            <div class="flex justify-between mr-2">
                <span class="text-3xl font-bold"> {{ board.name }} </span>
                <div class="controls bg-purple-700 rounded-full ml-10">
                    <button class="px-8 h-full rounded-full text-white capitalize bg-purple-400">List</button>
                    <button class="px-8 h-full rounded-full text-white capitalize">Kanban</button>
                </div>
            </div>
      </div>

      <div class="flex items-center">
        <button class="btn text-white bg-purple-700 hover:bg-purple-400 rounded-full" @click="createMode = !createMode">
          New Task
        </button>
        <input
          type="search"
          class="form-input ml-2 w-48"
          name=""
          id=""
          placeholder="search"
        />
        <span class="ml-2 toolbar-buttons"> <i class="fa fa-user"></i> </span>
        <span class="ml-2 toolbar-buttons"><i class="fa fa-eye"></i></span>
        <span class="ml-2 toolbar-buttons">
          <i class="fa fa-thumbtack"></i
        ></span>
        <span class="ml-2 toolbar-buttons"> <i class="fa fa-filter"></i> </span>
        <span class="ml-2 toolbar-buttons"> <i class="fa fa-sort"></i> </span>
      </div>
    </div>

    <div class="bg-white shadow-lg px-10 py-5">
        <draggable v-model="board.stages" @end="saveReorder">
            <transition-group>
                <item-group
                    v-for="stage in board.stages"
                    :key="stage.name"
                    :stage="stage"
                    :items="stage.items"
                    :create-mode="createMode"
                    @saved="addItem"
                    @item-deleted="confirmDeleteItem"
                    @stage-updated="addStage"
                    class="mt-4"
                    >
                </item-group>
            </transition-group>
        </draggable>

        <div class="w-full flex justify-center py-5">
            <button
                class="rounded-full flex justify-center items-center px-2 h-8 w-8 border-2 border-purple-400 text-purple-400 hover:bg-purple-400 hover:text-white"
                @click="addStage()"
                >

                <i class="fa fa-plus"></i>
            </button>
        </div>


        <!-- Delete Team Confirmation Modal -->
        <jet-confirmation-modal :show="itemToDelete" @close="itemToDelete = false">
            <template #title>
                Delete Team
            </template>

            <template #content>
                Are you sure you want to delete this team? Once a team is deleted, all of its resources and data will be permanently deleted.
            </template>

            <template #footer>
                <jet-secondary-button @click.native="itemToDelete = false">
                    Nevermind
                </jet-secondary-button>

                <jet-danger-button class="ml-2" @click.native="deleteItem(itemToDelete)">
                    Delete Item
                </jet-danger-button>
            </template>
        </jet-confirmation-modal>
    </div>

  </div>
</template>

<script>
import JetConfirmationModal from '../../Jetstream/ConfirmationModal'
import JetDangerButton from '../../Jetstream/DangerButton'
import JetSecondaryButton from '../../Jetstream/SecondaryButton'
import ItemGroup from "./ItemGroup.vue";
import Draggable from "vuedraggable";

export default {
  name: "Board",
  components: {
    ItemGroup,
    Draggable,
    JetConfirmationModal,
    JetDangerButton,
    JetSecondaryButton
  },
  props: {
    board: {
        type: Object,
        required: true
    }
  },
  data() {
    return {
      createMode: false,
      views: [],
      itemToDelete: false,
      items: [
        {
          title: "Test",
          owner: "Jesus Guerrero",
          status: "todo",
          due_date: new Date().toISOString().slice(0, 10),
          priority: "High"
        },
        {
          title: "Test",
          owner: "Jesus Guerrero",
          status: "todo",
          due_date: new Date().toISOString().slice(0, 10),
          priority: "low"
        },
        {
          title: "Test",
          owner: "Jesus Guerrero",
          status: "todo",
          due_date: new Date().toISOString().slice(0, 10),
          priority: "medium"
        }
      ],
      comments: [],
      contacts: [
        {
          name: "Jesus Guerrero"
        }
      ]
    };
  },
  methods: {
    addItem(item, reload=true) {
        const method = item.id ? 'PUT' : 'POST';
        const param = item.id ? `/${item.id}`: ''
        axios({
            url: `/items${param}`,
            method,
            data: item
        }).then(() => {
            if (reload) {
                this.$inertia.reload({ preserveScroll: true })
            }
        })
    },

    confirmDeleteItem(item, reload=true) {
        this.itemToDelete = item;
    },

    deleteItem(item, reload = true) {
        axios({
            url: `/items/${item.id}`,
            method: 'delete',
        }).then(() => {
            if (reload) {
                this.itemToDelete = false;
                this.$inertia.reload({ preserveScroll: true })
            }
        })
    },

    addStage(stage = {}, reload = true) {
        const method = stage.id ? 'PUT' : 'POST';
        const param = stage.id ? `/${stage.id}`: ''
        stage.board_id = this.board.id
        stage.name = stage.name || `Stage ${this.board.stages.length + 1}`

        return axios({
            url: `/stages${param}`,
            method,
            data: stage
        }).then(({ data }) => {
            if (reload) {
                this.$inertia.reload({ preserveScroll: true })
            }
        })
    },

    saveReorder() {
      this.board.stages.forEach(async (stage, index) => {
          stage.order = index;
          await this.addStage(stage, false);
      })
        this.$inertia.reload({ preserveScroll: true })
    },
  }
};
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style lang="scss">
h3 {
  margin: 40px 0 0;
}
ul {
  list-style-type: none;
  padding: 0;
}
li {
  display: inline-block;
  margin: 0 10px;
}
a {
  color: #42b983;
}

.btn {
  @apply font-bold py-2 px-4 rounded;
}


.toolbar-buttons {
  @apply px-2 rounded-full inline-flex items-center justify-center cursor-pointer;
  width: 34px;
  height: 34px;

  &:hover {
    @apply bg-gray-300;
  }
}

.form-input {
  @apply shadow appearance-none border rounded py-2 px-3 text-gray-700 leading-tight;
}
</style>
