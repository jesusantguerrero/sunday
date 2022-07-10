<template>
    <app-layout :boards="notebooks">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Dashboard
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-8xl mx-auto sm:px-6 lg:px-8 flex">
                <div class="about-screen">
                    <p class="close-bar">
                        <span class="close-about icon icon-left-open"> </span>
                    </p>
                    <div class="block-container">
                        <h3 class="logo cursor-pointer">
                            <span class="mr-4">IC</span><span>Dail<span class="movable">y.</span></span>
                        </h3>
                    </div>
                    <div class="block-container">
                        <p class="app-version"><span>{{state.appVersion}}</span></p>
                    </div>
                    <p class="about-nav">
                        <button
                            v-for="(section, sectionName) in state.sections"
                            :key="sectionName"
                            :class="{selected: sectionName == selectedSection}"
                            @click="state.selectedSection=sectionName"
                        >
                            {{ section }}
                        </button>
                    </p>

                    <div class="information-container">
                        <div class="prose lg:prose-md w-full max-w-full" v-html="currentSection" />
                    </div>

                    <div class="logos"></div>
                    <div class="block-container">
                        <p class="app-version">
                            Developed by
                            <span class="author">Jesus Guerrero</span>
                        </p>
                    </div>
                    <div class=""></div>
                </div>
            </div>
        </div>
    </app-layout>
</template>

<script setup>
import AppLayout from "./../Layouts/AppLayout.vue";
import info from "@docs/info.md"
import thanks from "@docs/thanksto.md"
import tutorial from "@docs/tutorial.md"
import { reactive, computed } from "vue";


const state = reactive({
        appVersion: "1.0.0",
        selectedSection: 'info',
        sections: {
            info: "Information",
            tutorial: "Tutorial",
            thanks: "Thanks to",
            licence: "License",
        },
        text: {
            info,
            thanks,
            tutorial
        }
});

const currentSection = computed(() => {
    return state.text[state.selectedSection]
})
</script>


<style lang="scss">
h1 {
    @apply text-xl font-bold;
}
</style>

<style lang="scss" scoped>
h1 {
    @apply text-lg;
    color: red;
}

.about-screen {
  width: 100%;
  text-align: center;
  overflow-y: auto;

    .block-container {
        display: flex;
        justify-content: center;
        width: 100%;
    }

   .logo {
       @apply text-gray-600;
        transition: all ease 1s;
        padding: 0;
        margin: 0;
        width: fit-content;
        font-size: 90px;
        overflow: hidden;
        font-weight: bolder;

        &:hover {
            color: dodgerblue;
            .movable {
                -webkit-transform: rotate(35deg);
                transform: rotate(35deg); }
            }
    }

}

  .about-screen .app-version {
    font-size: 24px;
    font-weight: bolder;
    color: #ccc; }
    .about-screen .app-version:hover {
      color: #999; }
    .about-screen .about-nav button {
        margin: 0 0 0 0;
        margin-top: 15px;
        padding: 10px;
        background: #ddd;
        border: 2px solid #ddd;
    }

.about-nav {
    button:first-child {
        border-radius: 4px 0 0 4px;
    }
    button:last-child {
        border-radius: 0 4px 4px 0;
    }
    button:hover {
        border: 2px solid #999;
    }
    button.selected {
        @apply bg-gray-500 border-gray-500 text-gray-100;
    }
}

.about-screen .information-container {
    text-align: left;
    width: 100%;
    padding: 20px 100px;
}

.about-screen .logos {
    margin-top: 70px;
}

.about-screen .logos img {
    width: 120px;
}
</style>
