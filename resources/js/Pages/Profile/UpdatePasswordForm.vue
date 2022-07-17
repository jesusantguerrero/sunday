<template>
    <jet-form-section @submitted="updatePassword">
        <template #title>
            Update Password
        </template>

        <template #description>
            Ensure your account is using a long, random password to stay secure.
        </template>

        <template #form>
            <div class="col-span-6 sm:col-span-4">
                <jet-label for="current_password" value="Current Password" />
                <jet-input id="current_password" type="password" class="block w-full mt-1" v-model="form.current_password" ref="current_password" autocomplete="current-password" />
                <jet-input-error :message="form.errors.current_password" class="mt-2" />
            </div>

            <div class="col-span-6 sm:col-span-4">
                <jet-label for="password" value="New Password" />
                <jet-input id="password" type="password" class="block w-full mt-1" v-model="form.password" autocomplete="new-password" />
                <jet-input-error :message="form.errors.password" class="mt-2" />
            </div>

            <div class="col-span-6 sm:col-span-4">
                <jet-label for="password_confirmation" value="Confirm Password" />
                <jet-input id="password_confirmation" type="password" class="block w-full mt-1" v-model="form.password_confirmation" autocomplete="new-password" />
                <jet-input-error :message="form.errors.password_confirmation" class="mt-2" />
            </div>
        </template>

        <template #actions>
            <jet-action-message :on="form.recentlySuccessful" class="mr-3">
                Saved.
            </jet-action-message>

            <jet-button :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                Save
            </jet-button>
        </template>
    </jet-form-section>
</template>

<script>
    import JetActionMessage from './../../Jetstream/ActionMessage.vue'
    import JetButton from './../../Jetstream/Button.vue'
    import JetFormSection from './../../Jetstream/FormSection.vue'
    import JetInput from './../../Jetstream/Input.vue'
    import JetInputError from './../../Jetstream/InputError.vue'
    import JetLabel from './../../Jetstream/Label.vue'

    export default {
        components: {
            JetActionMessage,
            JetButton,
            JetFormSection,
            JetInput,
            JetInputError,
            JetLabel,
        },

        data() {
            return {
                form: this.$inertia.form({
                    current_password: '',
                    password: '',
                    password_confirmation: '',
                }, {
                    bag: 'updatePassword',
                }),
            }
        },

        methods: {
            updatePassword() {
                this.form.put('/user/password', {
                    preserveScroll: true
                }).then(() => {
                    this.$refs.current_password.focus()
                })
            },
        },
    }
</script>
