<template>
    <jet-confirmation-modal
        :show="showModal"
        @close="$emit('close')"
    >
        <template #title>
            {{ modal.title }}
        </template>

        <template #content>
           <div>
               {{ modal.content }}
           </div>
        </template>

        <template #footer>
            <jet-secondary-button @click.native="$emit('close')">
                {{ modal.cancelButtonText }}
            </jet-secondary-button>

            <jet-danger-button
                class="ml-2"
                @click.native="sendConfirm()"
            >
                {{ modal.confirmationButtonText }}
            </jet-danger-button>
        </template>
    </jet-confirmation-modal>
</template>

<script>
import JetConfirmationModal from "../../Jetstream/ConfirmationModal";
import JetDangerButton from "../../Jetstream/DangerButton";
import JetSecondaryButton from "../../Jetstream/SecondaryButton";

export default {
    components: {
        JetConfirmationModal,
        JetDangerButton,
        JetSecondaryButton
    },
    props: {
        showModal: {
            type: Boolean
        },
        modalData: {
            type: Object,
            default() {
                return {}
            }
        }
    },
    data() {
        return {
            title: "Are you sure?",
            content: `Are you sure you want to delete this team? Once a team is
            deleted, all of its resources and data will be permanently
            deleted.`,
            data: null,
            cancelButtonText: 'Nevermind',
            confirmationButtonText: 'Delete Item',
        }
    },
    computed: {
        modal() {
            return {
                title: this.modalData.title ?? this.tile,
                content: this.modalData.content ?? this.content,
                data: this.modalData.data ?? this.data,
                cancelButtonText: this.modalData.cancelButtonText ?? this.cancelButtonText,
                confirmationButtonText: this.modalData.confirmationButtonText ?? this.confirmationButtonText
            }
        }
    },
    methods: {
        sendConfirm() {
            this.$emit('confirm', this.data)
        }
    }
}
</script>

<style>

</style>
