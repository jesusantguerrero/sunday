<template>
  <AtAuthBox background-image="/images/clouds.jpg">
    <AtAuthForm
      app-name="Sunday"
      btn-class="overflow-hidden text-white bg-primary hover:bg-primary/50"
      link-class="text-primary hover:text-primary/50"
      v-model:isLoading="form.processing"
      :errors="form.errors"
      @submit="submit"
      @home-pressed="onHomePressed"
      @link-pressed="onLinkPressed"
    >
      <template #brand>
        <AppLogo class="w-full h-20 text-white" />
      </template>
      <template #append>
        <AtButton>
            Connect with neatlancer
        </AtButton>
      </template>
    </AtAuthForm>
  </AtAuthBox>
    <AtButton @click="neatlancerLogin">
        Connect with neatlancer
    </AtButton>
</template>

<script setup>
import { AtAuthBox, AtAuthForm, AtButton } from "atmosphere-ui";
import { Inertia } from "@inertiajs/inertia";
import { useForm } from "@inertiajs/inertia-vue3";
import AppLogo from "@/Jetstream/ApplicationMark.vue";
import { onMounted } from "vue";

defineProps({
  canResetPassword: Boolean,
  status: String,
});

const form = useForm({
  email: "",
  password: "",
  remember: false,
});

const onHomePressed = () => {
  Inertia.visit("/");
};

const onLinkPressed = () => {
  Inertia.visit("register");
};

const submit = (formData) => {
  form
    .transform((data) => ({
      ...data,
      ...formData,
      remember: form.remember ? "on" : "",
    }))
    .post(route("login"), {
      onFinish: () => form.reset("password"),
    });
};

const neatlancerLogin = () => {
  window.location.href = '/neatlancer';
};

onMounted(() => {
    axios.get('/oauth/clients')
    .then(response => {
        console.log(response.data);
    });
})
</script>
