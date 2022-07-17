<template>
    <app-layout :boards="okrs">
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Dashboard
            </h2>
        </template>

        <div class="py-12" >
            <input type="text" v-model="message">
            <button  class="btn" @click="greet()">
                Greet
            </button>
        </div>
    </app-layout>
</template>

<script>
    import AppLayout from './../Layouts/AppLayout.vue'

    export default {
        name: "Integrations",
         props:{
            okrs: {
                type: Array,
                default() {
                    return []
                }
            }
        },
        data() {
            return {
                message: "Hello World"
            }
        },
        components: {
            AppLayout
        },
        methods: {
            greet() {
                const msg = new SpeechSynthesisUtterance(this.message);
                speechSynthesis.speak(msg);
            }
        }
    }
</script>
