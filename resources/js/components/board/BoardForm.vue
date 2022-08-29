<template>
    <DialogModal :show="isOpen" @close="$emit('closed')">
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
                    <multiselect
                        v-model="formData.BoardType"
                        ref="input"
                        :show-labels="false"
                        label="title"
                        :options="boardTypes"
                        class="w-full"
                    />
                </div>

                <div class="pt-2" v-if="isBoardType">
                    <label for="url"> Template </label>
                    <multiselect
                        v-model="formData.Template"
                        ref="input"
                        :show-labels="false"
                        label="name"
                        :options="boardTemplates"
                        class="w-full"
                    />
                </div>

                <div class="pt-2">
                    <label for="url"> Color </label>
                    <input type="text" class="form-control" v-model="formData.color">
                </div>
            </form>
        </template>

        <template #footer>
            <PrimaryButton @click="$emit('cancel')">
                Cancel
            </PrimaryButton>
            <PrimaryButton @click="save()">
                Save Board
            </PrimaryButton>
        </template>
    </DialogModal>
</template>

<script setup>
import { inject, computed, watch } from "vue";
import { useForm } from "@inertiajs/inertia-vue3";
import DialogModal from "@/Jetstream/DialogModal.vue"
import PrimaryButton from "@/Jetstream/Button.vue"

const props = defineProps({
    isOpen: {
        type: Boolean
    },
    recordData: {
        type: Object
    }
});

const emit = defineEmits(['save']);

const boardTypes = inject('boardTypes', []);
const boardTemplates = inject('boardTemplates', []);

const formData = useForm()

watch(() => props.recordData,
    (recordData) => {
        if (recordData) {
            Object.keys(props.recordData).forEach((field) => {
                formData[field] = props.recordData[field]
            })
        } else {
            formData.reset()
        }
    }, {
        deep: true,
        immediate: true
    }
);

const isBoardType = computed(() => {
    return formData.BoardType && formData.BoardType.name == 'board'
})

function save() {
    const method = formData.id ? "PUT" : "POST";
    const param = formData.id ? `/${formData.id}` : "";
    const form = {
        name: formData.name,
        board_type_id: formData?.BoardType?.id,
        board_template_id: formData?.Template?.id,
        color: formData?.color
    }
    if (form.name) {
        axios({
            url: "/api/boards",
            method: "post",
            data: form
        }).then(() => {
            emit('saved');
        });
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
