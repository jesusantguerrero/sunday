<template>
    <div
        class="bg-white  w-4/12 px-5 py-10 mb-5 mx-6 shadow-md rounded-md"
        :class="{ 'border-purple-400 border-2': isCurrent }"
    >
        <div class="prose prose-xl">
            <h3 class="text-center ">
                {{ plan.name }}
                <div
                    v-if="isCurrent"
                    class="rounded-md text-purple-600 px-1 py-1 text-xs"
                >
                    Current Plan
                </div>
            </h3>
        </div>

        <div class="px-5 py-2 my-2 rounded-md">
            <h2 class="text-5xl text-center">
                <span class="font-extrabold">
                    {{ plan.quantity }}
                </span>
                <small class="text-md">
                    USD
                </small>
            </h2>

            <div class="mt-5">
                <div class="prose prose-md">
                    Lorem ipsum dolor sit amet consectetur adipisicing elit.
                    Recusandae excepturi assumenda minus ad voluptates quo!
                    Voluptates, sint amet obcaecati quaerat tempora
                    exercitationem cum ullam ab quae. Optio labore debitis
                    voluptas?
                </div>
            </div>
        </div>

        <div class="text-center" v-if="!isCurrent">
            <a
                v-if="contactLink"
                class="border-2 border-purple-500 bg-white text-blue-500 px-5 py-2 inline-block rounded-md"
                :href="contactLink"
            >
                Contact Sales
            </a>


            <div ref="buttonsContainer" v-if="plan.paypal_plan_id">

            </div>
            <a v-else
                class="bg-gray-400 text-white px-5 py-2 inline-block rounded-sm w-full"
                :href="subscribeLink"
            >
                {{ subscribeLabel }}
            </a>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        plan: {
            type: Object,
            required: true
        },
        isCurrent: {
            type: Boolean,
            default: false
        },
        subscribeLink: {
            type: String,
            required: true
        },
        contactLink: {
            type: String,
        },
        subscribeLabel: {
            type: String,
            default: 'subscribe'
        }
    },
    mounted() {
        const self = this
        paypal.Buttons({
            createSubscription(data, actions) {
                return actions.subscription.create({
                    'plan_id': self.plan.paypal_plan_id
                });

            },

            onApprove(data, actions) {
                data.plan_id = self.plan.paypal_plan_id
                self.createSubscription(data)
            }

            }).render(this.$refs.buttonsContainer);
    },
    methods: {
        createSubscription(data) {
            console.log(data);
            axios({
                method: "POST",
                url: `/v2/subscriptions/${data.subscriptionID}/save`,
                data
            }).then(() => {
                this.fireworks();
            })
        }
    }
};
</script>

<style></style>
