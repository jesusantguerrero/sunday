<template>
    <app-layout :boards="notebooks">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Dashboard
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-8xl mx-auto sm:px-6 lg:px-8 flex">
               <div v-for="book in notebooks" :key="book.id">
                {{ book.name }}
               </div>
            </div>
        </div>
    </app-layout>
</template>

<script>
    import AppLayout from './../Layouts/AppLayout'

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
