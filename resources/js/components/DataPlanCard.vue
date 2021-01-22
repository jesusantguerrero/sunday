<template>
    <div
        class="bg-white px-5 py-10 mb-5 mx-2 rounded-md shadow-md rounded-md">
        <div class="">
            <div class="prose prose-xl mx-auto">
                <h4 class="text-center">{{ plan.name }}</h4>
            </div>
        </div>

        <div class="text-center">
            <div
                class="prose bg-cool-gray-600 text-white px-5 py-2 my-2 rounded-md mx-auto"
            ></div>
        </div>

        <div class="text-center">
            <button
                class="bg-cool-gray-500 text-white px-5 py-2 inline-block rounded-md"
                v-if="status == 'active'"
                @click="$emit('suspend')"
            >
                Suspend
            </button>
            <button
                class="bg-green-500 text-white px-5 py-2 inline-block rounded-md"
                v-if="status == 'suspended'"
                @click="$emit('reactivate')"
            >
                Reactivate
            </button>
            <button
                class="bg-red-500 text-white px-5 py-2 inline-block rounded-md"
                v-if="['active', 'suspended'].includes(status)"
                @click="$emit('cancel')"
            >
                cancel
            </button>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        plan: {
            type: Object,
            required: true
        }
    },
    computed: {
        status() {
            return this.plan.status && this.plan.status.toLowerCase()
        }
    }
};
</script>

<style></style>
