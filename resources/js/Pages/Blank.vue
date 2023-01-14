<template>
    <app-layout :boards="boards">
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Dashboard
            </h2>
        </template>

        <div class="py-12">
            <div class="flex mx-auto max-w-8xl sm:px-6 lg:px-8">
               Comming Soon
            </div>
        </div>
    </app-layout>
</template>

<script>
    import AppLayout from './../Layouts/AppLayout.vue'

    export default {
        name: "Integrations",
        components: {
            AppLayout
        }
    }
</script>

