<template>
    <dialog-modal :show="isOpen" @close="$emit('closed')">
        <template #title>
            Edit Item
        </template>

        <template #content>
            <form action="" @submit.prevent="save">
                <div class="form-group">
                    <label for="title"> Title </label>
                    <input type="text" class="form-control" v-model="formData.title">
                </div>
                <div class="form-group">
                    <label for="title"> Checklist </label>
                    <draggable
                        v-model="formData.checklist"
                        handle=".handle"
                    >
                        <div v-for="(check, index) in formData.checklist" :key="check.id" class="checklist-item">
                            <i class="fa fa-arrows-alt checklist-item__move handle"></i>
                            <input type="checkbox" class="form-control-check" v-model="check.done">
                            <input type="text" class="form-control checklist-item__input" v-model="check.title">
                            <i class="fa fa-trash checklist-item__delete" @click="deleteCheck(index)"></i>
                        </div>
                    </draggable>
                     <item-group-cell
                        class="w-full flex items-center"
                        field-name="title"
                        :is-title="true"
                        :index="-1"
                        :item="newCheck"
                        :is-new="true"
                        @saved="newCheck['title'] = $event"
                        @keydown.enter="addToCheckList()"
                    >
                    </item-group-cell>
                </div>
            </form>
        </template>

        <template #footer>
            <primary-button @click.native="$emit('cancel')">
                Cancel
            </primary-button>
            <primary-button @click.native="save()">
                Save
            </primary-button>
        </template>
    </dialog-modal>
</template>

<script>
import DialogModal from "../../Jetstream/DialogModal"
import ItemGroupCell from "./ItemGroupCell";
import PrimaryButton from "../../Jetstream/Button"
import Draggable from "vuedraggable";

export default {
    components: {
        ItemGroupCell,
        DialogModal,
        PrimaryButton,
        Draggable
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

            },
            newCheck: {}
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
                url: `/items${param}`,
                method,
                data: this.formData
            }).then(() => {
                this.$emit('saved')
            });
        },

        deleteCheck(index) {
            this.formData.checklist.splice(index, 1);
        },

        addToCheckList() {
            if (this.formData.checklist) {
                this.formData.checklist.push(this.newCheck)
                this.newCheck = {};
                return
            }
            this.formData.checklist = [this.newCheck];
            this.newCheck = {};
        }
    }
}
</script>

<style lang="scss">
    .form-control {
        @apply w-full bg-gray-100 border-gray-400 border-2 px-4;
        height: 37px;
        border-radius: 4px;
    }

    .checklist-item {
        display: flex;
        align-items: center;
        border: 1px solid #eee;
        border-left: 0;
        border-right: 0;
        padding: 0 5px;

        &__delete {
            color: #eee;
            transition: all ease .5s;
            cursor: pointer;
            &:hover {
                @apply text-red-400;
            }
        }

        &__move {
            margin-right: 5px;
            color: #eee;
            transition: all ease .5s;
            cursor: pointer;
            &:hover {
                @apply text-purple-400;
            }
        }

        &__input, form-control {
            border: none;
            background: white;

            &:hover, &:focus {
                outline: none;
            }
        }
    }

    h1 {
        @apply mb-2;
    }

    .form-group {
        @apply mx-2 mb-4;
    }

    .workflow-item {
        @apply border-2 border-gray-300;
        display: inline-block;
        margin: 2px;
        padding: 2px 5px;
        border-radius: 4px;
        cursor: pointer;
    }
</style>
