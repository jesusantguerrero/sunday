<template>
    <AtAuthBox>
        <AtAuthForm
            app-name="Loger"
            btn-class="overflow-hidden text-white bg-pink-500 rounded-lg hover:bg-pink-600"
            link-class="text-pink-500 hover:text-pink-600"
            v-model:isLoading="form.processing"
            :errors="form.errors"
            @submit="submit"
            @home-pressed="onHomePressed"
            @link-pressed="onLinkPressed"
        />
    </AtAuthBox>
</template>

<script setup>
    import { AtAuthBox, AtAuthForm } from "atmosphere-ui";
    import { Inertia } from "@inertiajs/inertia";
    import { useForm } from "@inertiajs/inertia-vue3";

    defineProps({
        canResetPassword: Boolean,
        status: String,
    });

    const form = useForm({
        email: '',
        password: '',
        remember: false
    })

    const onHomePressed = () => {
        Inertia.visit('/');
    }

    const onLinkPressed = () => {
        Inertia.visit('register');
    }

    const submit = (formData) => {
        form
            .transform(data => ({
                ...data,
                ... formData,
                remember: form.remember ? 'on' : ''
            }))
            .post(route('login'), {
                onFinish: () => form.reset('password'),
            })
    }
</script>
