<template>
    <dialog-modal :show="isOpen" @close="$emit('closed')">
        <template #title>
            Add Link
        </template>

        <template #content>
            <form action="" @submit.prevent="save">
                <div>
                    <label for="title"> Title </label>
                    <input type="text" class="form-control" v-model="formData.title">
                </div>
                <div class="pt-2">
                    <label for="url"> Url </label>
                    <input type="text" class="form-control" v-model="formData.url">
                </div>
            </form>
        </template>

        <template #footer>
            <primary-button @click.native="$emit('cancel')">
                Cancel
            </primary-button>
            <primary-button @click.native="save()">
                Save Link
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
            axios({
                url: `/links${param}`,
                method,
                data: this.formData
            }).then(() => {
                this.$emit('saved');
            });
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
