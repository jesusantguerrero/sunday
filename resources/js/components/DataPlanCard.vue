<template>
    <div
        class="bg-white px-5 py-10 mb-5 mx-2 rounded-md shadow-md rounded-md flex">
        <div class="w-4/12">
            <div class="mx-auto">
                <div class="prose prose-xl">
                    <h5 class="mb-2"> Plan Details</h5>
                    <div class="text-3xl">
                        <span class="text-purple-600 font-bold">
                            {{ plan.name }}
                        </span>

                        <div class="text-2xl text-gray-400">
                            Your plan information
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-center w-4/12">
            <div
                class="prose prose-xl px-5 py-2 my-2 rounded-md mx-auto"
            >
            <div v-if="plan.trials_ends_at">
                <h4 > Days left </h4>
                <p>
                    {{ plan.trials_ends_at }}
                </p>
            </div>

                <div class="mt-10 mb-2">
                    <h5> Plan Details</h5>
                    <p class="text-2x1 text-gray-400">
                        <span> <i class="fa fa-users"></i> 1 member(s) </span>
                        <span class="ml-2"> <i class="fa fa-users"></i> 1 team(s) </span>
                    </p>
                </div>
            </div>
        </div>

        <div class="text-center w-4/12">
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
