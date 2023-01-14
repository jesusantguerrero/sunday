<template>
<div class="flex pl-5 overflow-hidden text-gray-500 rounded">
    <input
        type="search"
        class="w-full p-2 focus:outline-none"
        :model-value="value"
        @update:modelValue="emitInput"
        placeholder="Filter boards ..."
    />
    <button class="p-2 text-white bg-purple-400">
        Search
    </button>
</div>
</template>


<script>
export default {
    props: {
        value: {
            type: String,
            required: true
        }
    },
    methods: {
        emitInput(evt) {
            this.$emit('input', evt.target.value)
        }
    },
}
</script>
