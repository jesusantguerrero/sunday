<template>
    <button :type="type" :class="buttonClass" class="inline-flex items-center px-4 py-3 text-xs font-semibold tracking-widest text-white uppercase transition duration-150 ease-in-out border border-transparent rounded-md focus:outline-none focus:shadow-outline-gray">
        <slot></slot>
    </button>
</template>

<script>
    const themes = {
        primary: 'bg-purple-800 hover:bg-purple-700 active:bg-purple-900 focus:border-purple-900',
        secondary: 'bg-gray-800 hover:bg-gray-700 active:bg-gray-900 focus:border-gray-900',
        danger: 'bg-red-800 hover:bg-red-700 active:bg-red-900 focus:border-red-900'
    }

    export default {
        props: {
            type: {
                type: String,
                default: 'submit',
            },
            buttonType: {
                type: String,
                default: 'primary'
            }
        },
        computed: {
            buttonClass() {
                const btnType = themes[this.buttonType] || themes.primary
                return [btnType];
            }
        }
    }
</script>
