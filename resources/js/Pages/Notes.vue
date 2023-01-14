<template>
    <app-layout :boards="notebooks">
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Dashboard
            </h2>
        </template>

        <div class="py-12">
            <div class="flex mx-auto max-w-8xl sm:px-6 lg:px-8">
               <div v-for="book in notebooks" :key="book.id">
                {{ book.name }}
               </div>
            </div>
        </div>
    </app-layout>
</template>

<script>
    import AppLayout from './../Layouts/AppLayout.vue'

    export default {
        name: "Notes",
        props:{
            notebooks: {
                type: Array,
                default() {
                    return []
                }
            }
        },
        components: {
            AppLayout
        }
    }
</script>
