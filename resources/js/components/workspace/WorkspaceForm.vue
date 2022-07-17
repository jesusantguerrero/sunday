<template>
    <dialog-modal :show="isOpen" @close="$emit('closed')">
        <template #title>
            Create Workspace
        </template>

        <template #content>
            <form action="" @submit.prevent="save">
                <div>
                   <workspace-icon size="md" :name="formData.name" :icon="formData.icon" />
                </div>
                <div>
                    <label for="title"> Workspace Name </label>
                    <input type="text" class="form-control" v-model="formData.name">
                </div>
            </form>
        </template>

        <template #footer>
            <jet-button button-type="secondary" @click.native="$emit('cancel')">
                Cancel
            </jet-button>
            <jet-button @click.native="save()">
                Create Workspace
            </jet-button>
        </template>
    </dialog-modal>
</template>

<script>
import DialogModal from "../../Jetstream/DialogModal.vue"
import JetButton from "../../Jetstream/Button.vue"
import WorkspaceIcon from "./WorkspaceIcon.vue"

export default {
    components: {
    DialogModal,
    JetButton,
    WorkspaceIcon
},
    props: {
        isOpen: {
            type: Boolean
        },
        recordData: {
            type: Object
        }
    },
    inject: ['boardTypes', 'boardTemplates'],
    data() {
        return {
            formData: {

            }
        }
    },
    watch: {
        recordData() {
            this.formData = this.recordData
        }
    },
    computed: {
        isBoardType() {
            return this.formData.BoardType && this.formData.BoardType.name == 'board'
        },
    },
    methods: {
        save() {
            const method = this.formData.id ? "PUT" : "POST";
            const param = this.formData.id ? `/${this.formData.id}` : "";
            const formData = {
                name: this.formData.name,
                is_public: this.formData?.is_public,
                color: this.formData?.color,
                icon: this.formData?.icon
            }
            if (formData.name) {
                axios({
                    url: "/api/workspaces",
                    method: "post",
                    data: formData
                }).then(() => {
                    this.$emit('saved');
                });
            }
        },
    }
}
</script>

<style lang="scss">
    .form-control {
        @apply w-full bg-gray-100 border-gray-400 border-2 px-4;
        height: 37px;
        border-radius: 4px;
    }
</style>
