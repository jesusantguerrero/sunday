<template>
    <dialog-modal :show="isOpen" @close="$emit('closed')">
        <template #title>
            Add Board
        </template>

        <template #content>
            <form action="" @submit.prevent="save">
                <div>
                    <label for="title"> Name </label>
                    <input type="text" class="form-control" v-model="formData.name">
                </div>

                <div class="pt-2">
                    <label for="url"> Type </label>
                    <input type="text" class="form-control" v-model="formData.BoardType">
                </div>

                <div class="pt-2">
                    <label for="url"> Template </label>
                    <input type="text" class="form-control" v-model="formData.Template">
                </div>

                <div class="pt-2">
                    <label for="url"> Color </label>
                    <input type="text" class="form-control" v-model="formData.color">
                </div>
            </form>
        </template>

        <template #footer>
            <primary-button @click.native="$emit('cancel')">
                Cancel
            </primary-button>
            <primary-button @click.native="save()">
                Save Board
            </primary-button>
        </template>
    </dialog-modal>
</template>

<script>
import DialogModal from "../../Jetstream/DialogModal"
import PrimaryButton from "../../Jetstream/Button"

export default {
    components: {
        DialogModal,
        PrimaryButton
    },
    props: {
        isOpen: {
            type: Boolean
        },
        recordData: {
            type: Object
        }
    },
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
    methods: {
        save() {
            const method = this.formData.id ? "PUT" : "POST";
            const param = this.formData.id ? `/${this.formData.id}` : "";
            const formData = {
                name: this.formData.name,
                board_type_id: this.formData.BoardType,
                board_template_id: this.formData.Template,
                color: this.formData.color
            }
            if (formData.name) {
                axios({
                    url: "/api/boards",
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
